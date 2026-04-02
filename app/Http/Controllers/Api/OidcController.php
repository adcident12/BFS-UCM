<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use App\Services\OAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OidcController extends Controller
{
    public function __construct(private readonly OAuthService $oauth) {}

    // ── GET /.well-known/openid-configuration ─────────────────────────────────

    public function discovery(): JsonResponse
    {
        $base = config('app.url');

        return response()->json([
            'issuer'                                => $base,
            'authorization_endpoint'                => "{$base}/oauth/authorize",
            'token_endpoint'                        => "{$base}/oauth/token",
            'userinfo_endpoint'                     => "{$base}/oauth/userinfo",
            'jwks_uri'                              => "{$base}/oauth/jwks",
            'revocation_endpoint'                   => "{$base}/oauth/token/revoke",
            'response_types_supported'              => ['code'],
            'grant_types_supported'                 => ['authorization_code', 'refresh_token', 'client_credentials'],
            'subject_types_supported'               => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'scopes_supported'                      => ['openid', 'profile', 'email', 'permissions', 'users:read'],
            'token_endpoint_auth_methods_supported' => ['client_secret_basic', 'client_secret_post'],
            'claims_supported'                      => [
                'sub', 'iss', 'aud', 'exp', 'iat', 'nonce',
                'name', 'preferred_username', 'email', 'email_verified',
                'department', 'title', 'is_admin', 'permissions',
            ],
            'code_challenge_methods_supported'      => ['S256', 'plain'],
        ])->withHeaders([
            'Access-Control-Allow-Origin'  => '*',
            'Cache-Control'                => 'public, max-age=86400',
        ]);
    }

    // ── GET /oauth/jwks ───────────────────────────────────────────────────────

    public function jwks(): JsonResponse
    {
        return response()->json($this->oauth->jwks())->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control'               => 'public, max-age=3600',
        ]);
    }

    // ── GET /oauth/userinfo ───────────────────────────────────────────────────

    /**
     * Returns claims about the authenticated user.
     * Requires a valid Bearer access token with at least the 'openid' scope.
     */
    public function userinfo(Request $request): JsonResponse
    {
        $token = $this->extractBearerToken($request);
        if (! $token) {
            return response()->json(['error' => 'invalid_token'], 401)
                ->withHeaders(['WWW-Authenticate' => 'Bearer realm="ucm"']);
        }

        $record = $this->oauth->validateAccessToken($token);
        if (! $record) {
            return response()->json(['error' => 'invalid_token', 'error_description' => 'Token is invalid or expired.'], 401)
                ->withHeaders(['WWW-Authenticate' => 'Bearer realm="ucm", error="invalid_token"']);
        }

        if (! $record->hasScope('openid')) {
            return response()->json(['error' => 'insufficient_scope'], 403);
        }

        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OIDC_USERINFO_ACCESSED,
            "OIDC userinfo เข้าถึงโดย {$record->user->username} ผ่าน client '{$record->client->name}'",
            ['client' => $record->client->slug, 'scopes' => $record->scopes],
            $record->user,
            'oauth_client',
            $record->client_id,
            $record->client->name,
            $request,
        );

        return response()->json($this->oauth->userinfoFor($record))->withHeaders([
            'Cache-Control' => 'no-store',
        ]);
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }
}
