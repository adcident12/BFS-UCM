<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OAuthAccessToken;
use App\Models\System;
use App\Services\OAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Resource API for the authenticated user.
 * All endpoints require a valid OAuth Bearer token.
 */
class MeController extends Controller
{
    public function __construct(private readonly OAuthService $oauth) {}

    // ── GET /api/v1/me ────────────────────────────────────────────────────────

    /**
     * Returns the profile of the currently authenticated user.
     * Required scope: profile (or openid)
     */
    public function show(Request $request): JsonResponse
    {
        $tokenRecord = $this->requireToken($request, ['profile', 'openid']);
        if ($tokenRecord instanceof JsonResponse) {
            return $tokenRecord;
        }

        $user = $tokenRecord->user;

        return response()->json([
            'id'              => $user->id,
            'username'        => $user->username,
            'name'            => $user->name,
            'email'           => $user->email,
            'department'      => $user->department,
            'title'           => $user->title,
            'employee_number' => $user->employee_number,
            'is_admin'        => (int) $user->is_admin,
            'is_active'       => (bool) $user->is_active,
            'last_login_at'   => $user->last_login_at?->toIso8601String(),
        ]);
    }

    // ── GET /api/v1/me/permissions ────────────────────────────────────────────

    /**
     * Returns the user's permissions for the system tied to the OAuth client.
     * Required scope: permissions
     */
    public function permissions(Request $request): JsonResponse
    {
        $tokenRecord = $this->requireToken($request, ['permissions']);
        if ($tokenRecord instanceof JsonResponse) {
            return $tokenRecord;
        }

        $user   = $tokenRecord->user;
        $system = System::where('slug', $tokenRecord->client->slug)->first();

        if (! $system) {
            return response()->json([
                'permissions' => [],
                'note'        => 'No UCM system is mapped to this OAuth client slug.',
            ]);
        }

        $permissions = $user->getPermissionsForSystem($system->id);

        return response()->json([
            'system'      => [
                'id'   => $system->id,
                'slug' => $system->slug,
                'name' => $system->name,
            ],
            'permissions' => $permissions,
        ]);
    }

    // ── GET /api/v1/me/systems ────────────────────────────────────────────────

    /**
     * Returns all systems the user has at least one permission in.
     * Required scope: profile
     */
    public function systems(Request $request): JsonResponse
    {
        $tokenRecord = $this->requireToken($request, ['profile']);
        if ($tokenRecord instanceof JsonResponse) {
            return $tokenRecord;
        }

        $user    = $tokenRecord->user;
        $systems = System::where('is_active', true)->get();

        $result = $systems
            ->map(fn ($sys) => [
                'id'          => $sys->id,
                'slug'        => $sys->slug,
                'name'        => $sys->name,
                'permissions' => $user->getPermissionsForSystem($sys->id),
            ])
            ->filter(fn ($item) => ! empty($item['permissions']))
            ->values();

        return response()->json(['systems' => $result]);
    }

    // ── Private ───────────────────────────────────────────────────────────────

    /**
     * Validate Bearer token and check required scopes.
     * Returns the token record or a JsonResponse error.
     *
     * @param  string[]  $requiredScopes  At least ONE of these scopes must be present.
     */
    private function requireToken(Request $request, array $requiredScopes): OAuthAccessToken|JsonResponse
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

        foreach ($requiredScopes as $scope) {
            if ($record->hasScope($scope)) {
                return $record;
            }
        }

        return response()->json([
            'error'             => 'insufficient_scope',
            'error_description' => 'Required scope: '.implode(' or ', $requiredScopes),
            'required_scopes'   => $requiredScopes,
        ], 403);
    }
}
