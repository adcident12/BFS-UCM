<?php

namespace App\Http\Middleware;

use App\Services\OAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level middleware that validates a Bearer access token and checks
 * that it carries at least one of the required scopes.
 *
 * Usage in routes:
 *   ->middleware('oauth.scope:profile')
 *   ->middleware('oauth.scope:profile,openid')   // any one of these is sufficient
 */
class CheckOAuthScope
{
    public function __construct(private readonly OAuthService $oauth) {}

    public function handle(Request $request, Closure $next, string ...$requiredScopes): Response
    {
        $header = $request->header('Authorization', '');
        $raw    = str_starts_with($header, 'Bearer ') ? substr($header, 7) : null;

        if (! $raw) {
            return response()->json([
                'error'             => 'unauthorized',
                'error_description' => 'Bearer token required.',
            ], 401)->withHeaders(['WWW-Authenticate' => 'Bearer realm="ucm"']);
        }

        $record = $this->oauth->validateAccessToken($raw);

        if (! $record) {
            return response()->json([
                'error'             => 'invalid_token',
                'error_description' => 'Token is invalid or expired.',
            ], 401)->withHeaders(['WWW-Authenticate' => 'Bearer realm="ucm", error="invalid_token"']);
        }

        if (! empty($requiredScopes)) {
            $hasScope = false;
            foreach ($requiredScopes as $scope) {
                if ($record->hasScope($scope)) {
                    $hasScope = true;
                    break;
                }
            }

            if (! $hasScope) {
                return response()->json([
                    'error'             => 'insufficient_scope',
                    'error_description' => 'Required scope: '.implode(' or ', $requiredScopes),
                    'required_scopes'   => $requiredScopes,
                ], 403);
            }
        }

        // Attach token record to request for downstream use
        $request->attributes->set('oauth_token', $record);

        return $next($request);
    }
}
