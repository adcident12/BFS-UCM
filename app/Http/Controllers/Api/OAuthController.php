<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\OAuthClient;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use App\Services\OAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OAuthController extends Controller
{
    public function __construct(private readonly OAuthService $oauth) {}

    // ── GET /oauth/authorize ──────────────────────────────────────────────────

    /**
     * Display the consent screen (or redirect directly if auto_approve).
     * Requires an active UCM web session.
     */
    public function authorize(Request $request): View|RedirectResponse
    {
        // Validate required OAuth parameters up front
        $params = $request->validate([
            'response_type'         => ['required', 'in:code'],
            'client_id'             => ['required', 'string'],
            'redirect_uri'          => ['required', 'url'],
            'scope'                 => ['required', 'string'],
            'state'                 => ['nullable', 'string', 'max:256'],
            'nonce'                 => ['nullable', 'string', 'max:256'],
            'code_challenge'        => ['nullable', 'string'],
            'code_challenge_method' => ['nullable', 'in:S256,plain'],
        ]);

        $client = OAuthClient::where('client_id', $params['client_id'])->active()->first();

        if (! $client) {
            return $this->oauthError($params['redirect_uri'], 'invalid_client', 'Unknown client.', $params['state'] ?? null);
        }

        if (! $client->allowsRedirectUri($params['redirect_uri'])) {
            // Do NOT redirect to the URI — show plain error instead
            abort(400, 'redirect_uri is not registered for this client.');
        }

        $scopes = array_filter(explode(' ', $params['scope']));
        foreach ($scopes as $scope) {
            if (! $client->allowsScope($scope)) {
                return $this->oauthError($params['redirect_uri'], 'invalid_scope', "Scope '{$scope}' not allowed.", $params['state'] ?? null);
            }
        }

        // Ensure the user is logged in to UCM web session
        if (! Auth::check()) {
            session(['oauth_pending' => $request->fullUrl()]);

            return redirect()->route('login');
        }

        /** @var \App\Models\UcmUser $user */
        $user = Auth::user();

        // Auto-approve trusted clients — skip consent screen
        if ($client->auto_approve) {
            return $this->issueCodeAndRedirect($user, $client, $scopes, $params);
        }

        return view('oauth.authorize', compact('client', 'scopes', 'params', 'user'));
    }

    // ── POST /oauth/authorize ─────────────────────────────────────────────────

    /**
     * Process consent form submission (approve or deny).
     */
    public function approveAuthorization(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            abort(401);
        }

        $request->validate([
            'client_id'             => ['required', 'string'],
            'redirect_uri'          => ['required', 'url'],
            'scope'                 => ['required', 'string'],
            'state'                 => ['nullable', 'string'],
            'nonce'                 => ['nullable', 'string'],
            'code_challenge'        => ['nullable', 'string'],
            'code_challenge_method' => ['nullable', 'in:S256,plain'],
            'action'                => ['required', 'in:approve,deny'],
        ]);

        if ($request->input('action') === 'deny') {
            AuditLogger::log(
                AuditLog::CATEGORY_OAUTH,
                AuditLog::EVENT_OAUTH_DENIED,
                "ผู้ใช้ {$request->user()->username} ปฏิเสธการอนุญาต client '{$request->input('client_id')}'",
                ['client_id' => $request->input('client_id'), 'scopes' => explode(' ', $request->input('scope', ''))],
                $request->user(),
                'oauth_client',
                null,
                $request->input('client_id'),
                $request,
            );

            app(NotificationService::class)->dispatch('oauth_denied', [
                'client_id' => $request->input('client_id'),
                'username'  => $request->user()?->username,
            ]);

            return $this->oauthError(
                $request->input('redirect_uri'),
                'access_denied',
                'User denied authorization.',
                $request->input('state')
            );
        }

        $client = OAuthClient::where('client_id', $request->input('client_id'))->active()->first();

        if (! $client || ! $client->allowsRedirectUri($request->input('redirect_uri'))) {
            abort(400);
        }

        $scopes = array_filter(explode(' ', $request->input('scope')));

        return $this->issueCodeAndRedirect(Auth::user(), $client, $scopes, $request->all());
    }

    // ── POST /oauth/token ─────────────────────────────────────────────────────

    /**
     * Token endpoint — handles authorization_code, refresh_token, client_credentials.
     */
    public function token(Request $request): JsonResponse
    {
        $grantType = $request->input('grant_type');

        try {
            $result = match ($grantType) {
                'authorization_code' => $this->handleAuthCodeGrant($request),
                'refresh_token'      => $this->handleRefreshGrant($request),
                'client_credentials' => $this->handleClientCredentialsGrant($request),
                default              => throw new \InvalidArgumentException('unsupported_grant_type'),
            };
        } catch (\InvalidArgumentException $e) {
            Log::info('OAuth token error', ['grant' => $grantType, 'error' => $e->getMessage()]);

            return response()->json([
                'error'             => $this->extractErrorCode($e->getMessage()),
                'error_description' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('OAuth token exception', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'server_error'], 500);
        }

        // Audit log is written inside each grant handler
        return response()->json($result)->withHeaders([
            'Cache-Control' => 'no-store',
            'Pragma'        => 'no-cache',
        ]);
    }

    // ── DELETE /oauth/token ───────────────────────────────────────────────────

    /**
     * Revoke an access or refresh token (RFC 7009).
     */
    public function revoke(Request $request): JsonResponse
    {
        $token = $request->input('token');
        if (! $token) {
            return response()->json(['error' => 'invalid_request'], 400);
        }

        // Authenticate the client before revoking
        $client = $this->resolveClientFromRequest($request);
        if (! $client) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        $revoked = $this->oauth->revokeToken($token);

        if ($revoked) {
            AuditLogger::log(
                AuditLog::CATEGORY_OAUTH,
                AuditLog::EVENT_OAUTH_TOKEN_REVOKED,
                "Revoke OAuth token ผ่าน client '{$client->name}'",
                ['client' => $client->slug],
                null,
                'oauth_client',
                $client->id,
                $client->name,
                $request,
            );
        }

        // RFC 7009: always return 200 (even if token was unknown)
        return response()->json([], 200);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function handleAuthCodeGrant(Request $request): array
    {
        $client = $this->resolveClientFromRequest($request);
        if (! $client) {
            throw new \InvalidArgumentException('invalid_client');
        }

        $code        = $request->input('code');
        $redirectUri = $request->input('redirect_uri');
        $verifier    = $request->input('code_verifier');

        if (! $code || ! $redirectUri) {
            throw new \InvalidArgumentException('invalid_request: code and redirect_uri required');
        }

        $tokens = $this->oauth->exchangeCodeForTokens($code, $client, $redirectUri, $verifier);

        // Decode the issued token to find the user
        $tokenRecord = $this->oauth->validateAccessToken($tokens['access_token']);
        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_TOKEN_ISSUED,
            "ออก OAuth token (authorization_code) ให้ {$tokenRecord?->user?->username} ผ่าน client '{$client->name}'",
            ['grant_type' => 'authorization_code', 'client' => $client->slug, 'scopes' => $tokenRecord?->scopes ?? []],
            $tokenRecord?->user,
            'oauth_client',
            $client->id,
            $client->name,
            $request,
        );

        app(NotificationService::class)->dispatch('oauth_token_issued', [
            'grant_type'  => 'authorization_code',
            'client_name' => $client->name,
            'username'    => $tokenRecord?->user?->username,
        ]);

        return $tokens;
    }

    private function handleRefreshGrant(Request $request): array
    {
        $client = $this->resolveClientFromRequest($request);
        if (! $client) {
            throw new \InvalidArgumentException('invalid_client');
        }

        $rawToken = $request->input('refresh_token');
        if (! $rawToken) {
            throw new \InvalidArgumentException('invalid_request: refresh_token required');
        }

        $tokens = $this->oauth->refreshTokens($rawToken, $client);

        $newRecord = $this->oauth->validateAccessToken($tokens['access_token']);
        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_TOKEN_REFRESHED,
            "Refresh OAuth token สำหรับ {$newRecord?->user?->username} ผ่าน client '{$client->name}'",
            ['grant_type' => 'refresh_token', 'client' => $client->slug, 'scopes' => $newRecord?->scopes ?? []],
            $newRecord?->user,
            'oauth_client',
            $client->id,
            $client->name,
            $request,
        );

        return $tokens;
    }

    private function handleClientCredentialsGrant(Request $request): array
    {
        $client = $this->resolveClientFromRequest($request);
        if (! $client || ! $client->is_confidential) {
            throw new \InvalidArgumentException('invalid_client');
        }

        $scopes = array_filter(explode(' ', (string) $request->input('scope', '')));

        $tokens = $this->oauth->clientCredentialsToken($client, $scopes);

        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_TOKEN_ISSUED,
            "ออก OAuth token (client_credentials) สำหรับ client '{$client->name}'",
            ['grant_type' => 'client_credentials', 'client' => $client->slug, 'scopes' => array_values($scopes)],
            null,
            'oauth_client',
            $client->id,
            $client->name,
            $request,
        );

        app(NotificationService::class)->dispatch('oauth_token_issued', [
            'grant_type'  => 'client_credentials',
            'client_name' => $client->name,
        ]);

        return $tokens;
    }

    /**
     * Resolve client identity from HTTP Basic auth or request body (client_id + client_secret).
     */
    private function resolveClientFromRequest(Request $request): ?OAuthClient
    {
        // HTTP Basic auth (preferred)
        $clientId     = $request->getUser() ?? $request->input('client_id');
        $clientSecret = $request->getPassword() ?? $request->input('client_secret');

        if (! $clientId) {
            return null;
        }

        $client = OAuthClient::where('client_id', $clientId)->active()->first();

        if (! $client) {
            return null;
        }

        // Public clients: no secret required, but must use PKCE
        if (! $client->is_confidential) {
            return $client;
        }

        // Confidential clients: verify secret
        if (! $clientSecret || ! $client->verifySecret($clientSecret)) {
            return null;
        }

        return $client;
    }

    private function issueCodeAndRedirect(
        $user,
        OAuthClient $client,
        array $scopes,
        array $params
    ): RedirectResponse {
        AuditLogger::log(
            AuditLog::CATEGORY_OAUTH,
            AuditLog::EVENT_OAUTH_AUTHORIZED,
            "ผู้ใช้ {$user->username} อนุญาตการเข้าถึงให้ client '{$client->name}' (scopes: ".implode(', ', $scopes).')',
            ['client' => $client->slug, 'scopes' => $scopes, 'auto_approve' => $client->auto_approve],
            $user,
            'oauth_client',
            $client->id,
            $client->name,
        );

        app(NotificationService::class)->dispatch('oauth_authorized', [
            'client_name' => $client->name,
            'username'    => $user->username,
            'scopes'      => implode(', ', $scopes),
        ]);

        $code = $this->oauth->createAuthorizationCode(
            user: $user,
            client: $client,
            scopes: $scopes,
            redirectUri: $params['redirect_uri'],
            codeChallenge: $params['code_challenge'] ?? null,
            codeChallengeMethod: $params['code_challenge_method'] ?? 'S256',
            state: $params['state'] ?? null,
            nonce: $params['nonce'] ?? null
        );

        $query = ['code' => $code];
        if (! empty($params['state'])) {
            $query['state'] = $params['state'];
        }

        return redirect()->away($params['redirect_uri'].'?'.http_build_query($query));
    }

    private function oauthError(string $redirectUri, string $error, string $description, ?string $state): RedirectResponse
    {
        $query = ['error' => $error, 'error_description' => $description];
        if ($state) {
            $query['state'] = $state;
        }

        return redirect()->away($redirectUri.'?'.http_build_query($query));
    }

    private function extractErrorCode(string $message): string
    {
        // Messages formatted as "error_code: description" or just "error_code"
        return explode(':', $message)[0];
    }
}
