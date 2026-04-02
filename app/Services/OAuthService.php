<?php

namespace App\Services;

use App\Models\OAuthAccessToken;
use App\Models\OAuthAuthorizationCode;
use App\Models\OAuthClient;
use App\Models\OAuthRefreshToken;
use App\Models\System;
use App\Models\UcmUser;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Core OAuth 2.0 + OIDC service.
 *
 * Security guarantees:
 *  - Authorization codes: 64-byte random, single-use, 10-minute TTL
 *  - Access tokens: RS256 JWT, jti stored in DB for revocation checks
 *  - Refresh tokens: 64-byte random, SHA-256 hash stored, rotated on each use
 *  - PKCE (S256): mandatory for public clients, optional for confidential
 *  - State parameter: validated by caller to prevent CSRF
 */
class OAuthService
{
    private const ACCESS_TOKEN_TTL  = 3600;     // 1 hour
    private const REFRESH_TOKEN_TTL = 2592000;  // 30 days
    private const AUTH_CODE_TTL     = 600;      // 10 minutes

    // ── RSA keys ──────────────────────────────────────────────────────────────

    private function privateKey(): string
    {
        $path = storage_path('app/oauth/private.pem');
        if (! file_exists($path)) {
            throw new RuntimeException('OAuth private key missing. Run: php artisan oauth:generate-keys');
        }

        return file_get_contents($path);
    }

    private function publicKey(): string
    {
        $path = storage_path('app/oauth/public.pem');
        if (! file_exists($path)) {
            throw new RuntimeException('OAuth public key missing. Run: php artisan oauth:generate-keys');
        }

        return file_get_contents($path);
    }

    // ── Authorization Code ────────────────────────────────────────────────────

    public function createAuthorizationCode(
        UcmUser $user,
        OAuthClient $client,
        array $scopes,
        string $redirectUri,
        ?string $codeChallenge = null,
        string $codeChallengeMethod = 'S256',
        ?string $state = null,
        ?string $nonce = null
    ): string {
        if (! $client->is_confidential && ! $codeChallenge) {
            throw new InvalidArgumentException('Public clients must use PKCE.');
        }

        $code = Str::random(64);

        OAuthAuthorizationCode::create([
            'code'                  => $code,
            'client_id'             => $client->id,
            'user_id'               => $user->id,
            'scopes'                => $scopes,
            'redirect_uri'          => $redirectUri,
            'expires_at'            => now()->addSeconds(self::AUTH_CODE_TTL),
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => $codeChallenge ? $codeChallengeMethod : null,
            'state'                 => $state,
            'nonce'                 => $nonce,
            'is_used'               => false,
            'created_at'            => now(),
        ]);

        return $code;
    }

    // ── Token Exchange — Authorization Code ───────────────────────────────────

    /**
     * @return array{access_token:string, token_type:string, expires_in:int, refresh_token:string, id_token:string|null, scope:string}
     */
    public function exchangeCodeForTokens(
        string $rawCode,
        OAuthClient $client,
        string $redirectUri,
        ?string $codeVerifier = null
    ): array {
        $record = OAuthAuthorizationCode::where('code', $rawCode)
            ->where('client_id', $client->id)
            ->first();

        if (! $record || ! $record->isValid()) {
            throw new InvalidArgumentException('invalid_grant');
        }

        if ($record->redirect_uri !== $redirectUri) {
            throw new InvalidArgumentException('invalid_grant: redirect_uri mismatch');
        }

        if ($record->code_challenge) {
            if (! $codeVerifier) {
                throw new InvalidArgumentException('invalid_grant: code_verifier required');
            }
            if (! $this->verifyPKCE($codeVerifier, $record->code_challenge, $record->code_challenge_method)) {
                throw new InvalidArgumentException('invalid_grant: PKCE verification failed');
            }
        }

        // Mark used BEFORE issuing tokens to prevent replay attacks
        $record->update(['is_used' => true]);

        $user   = $record->user;
        $scopes = $record->scopes;

        [$jwt, $tokenRecord] = $this->issueAccessToken($user, $client, $scopes);
        $refreshToken = $this->issueRefreshToken($tokenRecord);
        $idToken      = in_array('openid', $scopes, true)
            ? $this->buildIdToken($user, $client, $scopes, $record->nonce)
            : null;

        return [
            'access_token'  => $jwt,
            'token_type'    => 'Bearer',
            'expires_in'    => self::ACCESS_TOKEN_TTL,
            'refresh_token' => $refreshToken,
            'id_token'      => $idToken,
            'scope'         => implode(' ', $scopes),
        ];
    }

    // ── Token Exchange — Client Credentials (M2M) ────────────────────────────

    /**
     * @return array{access_token:string, token_type:string, expires_in:int, scope:string}
     */
    public function clientCredentialsToken(OAuthClient $client, array $requestedScopes): array
    {
        if (! $client->allowsGrantType('client_credentials')) {
            throw new InvalidArgumentException('unauthorized_client');
        }

        $scopes = array_values(array_filter(
            $requestedScopes,
            fn ($s) => $client->allowsScope($s)
        ));

        [$jwt] = $this->issueAccessToken(null, $client, $scopes);

        return [
            'access_token' => $jwt,
            'token_type'   => 'Bearer',
            'expires_in'   => self::ACCESS_TOKEN_TTL,
            'scope'        => implode(' ', $scopes),
        ];
    }

    // ── Token Refresh ─────────────────────────────────────────────────────────

    /**
     * @return array{access_token:string, token_type:string, expires_in:int, refresh_token:string, scope:string}
     */
    public function refreshTokens(string $rawRefreshToken, OAuthClient $client): array
    {
        $hash   = hash('sha256', $rawRefreshToken);
        $record = OAuthRefreshToken::where('token_hash', $hash)
            ->with('accessToken.user')
            ->first();

        if (! $record || ! $record->isValid()) {
            throw new InvalidArgumentException('invalid_grant');
        }

        if ($record->accessToken->client_id !== $client->id) {
            throw new InvalidArgumentException('invalid_grant: client mismatch');
        }

        // Revoke old pair (token rotation)
        $record->accessToken->revoke();

        $user   = $record->accessToken->user;
        $scopes = $record->accessToken->scopes;

        [$jwt, $newRecord] = $this->issueAccessToken($user, $client, $scopes);
        $newRefreshToken   = $this->issueRefreshToken($newRecord);

        return [
            'access_token'  => $jwt,
            'token_type'    => 'Bearer',
            'expires_in'    => self::ACCESS_TOKEN_TTL,
            'refresh_token' => $newRefreshToken,
            'scope'         => implode(' ', $scopes),
        ];
    }

    // ── Token Validation ──────────────────────────────────────────────────────

    public function validateAccessToken(string $rawToken): ?OAuthAccessToken
    {
        try {
            $decoded = JWT::decode($rawToken, new Key($this->publicKey(), 'RS256'));
        } catch (\Throwable $e) {
            Log::debug('OAuth: JWT decode failed', ['error' => $e->getMessage()]);

            return null;
        }

        $jti = $decoded->jti ?? null;
        if (! $jti) {
            return null;
        }

        $record = OAuthAccessToken::where('jti', $jti)->with(['user', 'client'])->first();

        return ($record && $record->isValid()) ? $record : null;
    }

    public function revokeToken(string $rawToken): bool
    {
        try {
            $decoded = JWT::decode($rawToken, new Key($this->publicKey(), 'RS256'));
            $jti     = $decoded->jti ?? null;
        } catch (\Throwable) {
            // Try as raw refresh token
            $hash   = hash('sha256', $rawToken);
            $record = OAuthRefreshToken::where('token_hash', $hash)->first();

            return $record ? (bool) $record->revoke() : false;
        }

        if (! $jti) {
            return false;
        }

        $record = OAuthAccessToken::where('jti', $jti)->first();

        return $record ? (bool) $record->revoke() : false;
    }

    // ── OIDC ─────────────────────────────────────────────────────────────────

    public function buildIdToken(UcmUser $user, OAuthClient $client, array $scopes, ?string $nonce = null): string
    {
        $now    = time();
        $claims = [
            'iss' => config('app.url'),
            'sub' => (string) $user->id,
            'aud' => $client->client_id,
            'iat' => $now,
            'exp' => $now + self::ACCESS_TOKEN_TTL,
        ];

        if ($nonce) {
            $claims['nonce'] = $nonce;
        }

        if (in_array('profile', $scopes, true)) {
            $claims['name']       = $user->name;
            $claims['username']   = $user->username;
            $claims['department'] = $user->department;
            $claims['title']      = $user->title;
        }

        if (in_array('email', $scopes, true)) {
            $claims['email']          = $user->email;
            $claims['email_verified'] = true;
        }

        return JWT::encode($claims, $this->privateKey(), 'RS256');
    }

    /**
     * @return array<string, mixed>
     */
    public function userinfoFor(OAuthAccessToken $tokenRecord): array
    {
        $user   = $tokenRecord->user;
        $scopes = $tokenRecord->scopes;

        $claims = ['sub' => (string) $user->id];

        if (in_array('profile', $scopes, true)) {
            $claims['name']               = $user->name;
            $claims['preferred_username'] = $user->username;
            $claims['department']         = $user->department;
            $claims['title']              = $user->title;
            $claims['is_admin']           = (int) $user->is_admin;
            $claims['updated_at']         = $user->updated_at?->timestamp;
        }

        if (in_array('email', $scopes, true)) {
            $claims['email']          = $user->email;
            $claims['email_verified'] = true;
        }

        if (in_array('permissions', $scopes, true)) {
            $system              = System::where('slug', $tokenRecord->client->slug)->first();
            $claims['permissions'] = $system
                ? $user->getPermissionsForSystem($system->id)
                : [];
        }

        return $claims;
    }

    /**
     * @return array<string, mixed>
     */
    public function jwks(): array
    {
        $pubKey = openssl_pkey_get_public($this->publicKey());
        $detail = openssl_pkey_get_details($pubKey);
        $rsa    = $detail['rsa'];

        return [
            'keys' => [[
                'kty' => 'RSA',
                'use' => 'sig',
                'alg' => 'RS256',
                'kid' => 'ucm-rs256-1',
                'n'   => rtrim(strtr(base64_encode($rsa['n']), '+/', '-_'), '='),
                'e'   => rtrim(strtr(base64_encode($rsa['e']), '+/', '-_'), '='),
            ]],
        ];
    }

    // ── PKCE ──────────────────────────────────────────────────────────────────

    public function verifyPKCE(string $verifier, string $challenge, ?string $method): bool
    {
        $method ??= 'S256';

        return match ($method) {
            'S256'  => hash_equals(
                rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
                $challenge
            ),
            'plain' => hash_equals($verifier, $challenge),
            default => false,
        };
    }

    // ── Private ───────────────────────────────────────────────────────────────

    /**
     * @return array{string, OAuthAccessToken}
     */
    private function issueAccessToken(?UcmUser $user, OAuthClient $client, array $scopes): array
    {
        $jti = Str::random(32);
        $now = time();

        $jwt = JWT::encode([
            'iss'    => config('app.url'),
            'sub'    => $user ? (string) $user->id : 'client:'.$client->client_id,
            'aud'    => $client->client_id,
            'iat'    => $now,
            'exp'    => $now + self::ACCESS_TOKEN_TTL,
            'jti'    => $jti,
            'scopes' => $scopes,
        ], $this->privateKey(), 'RS256');

        $record = OAuthAccessToken::create([
            'jti'        => $jti,
            'client_id'  => $client->id,
            'user_id'    => $user?->id,
            'scopes'     => $scopes,
            'expires_at' => now()->addSeconds(self::ACCESS_TOKEN_TTL),
        ]);

        return [$jwt, $record];
    }

    private function issueRefreshToken(OAuthAccessToken $tokenRecord): string
    {
        $raw = Str::random(64);

        OAuthRefreshToken::create([
            'token_hash'      => hash('sha256', $raw),
            'access_token_id' => $tokenRecord->id,
            'expires_at'      => now()->addSeconds(self::REFRESH_TOKEN_TTL),
            'created_at'      => now(),
        ]);

        return $raw;
    }
}
