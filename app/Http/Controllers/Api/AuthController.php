<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\System;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use App\Services\LdapService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * POST /api/auth/token
     *
     * Legacy systems call this endpoint to obtain a Sanctum API token.
     * They authenticate with a UCM admin account (username + password via LDAP).
     *
     * Request body:
     *   { "username": "admin", "password": "secret", "token_name": "repair-system" }
     *
     * Response:
     *   { "token": "...", "type": "Bearer" }
     */
    public function issueToken(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'token_name' => 'required|string|max:100',
        ]);

        // Verify credentials via Laravel's auth guard (uses LdapUserProvider)
        if (! auth()->attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var UcmUser $user */
        $user = auth()->user();

        if (! $user->isAdmin()) {
            auth()->logout();

            return response()->json(['message' => 'Forbidden: admin account required'], 403);
        }

        // Revoke existing token with same name to avoid duplicates
        $user->tokens()->where('name', $request->token_name)->delete();

        $token = $user->createToken($request->token_name);

        AuditLogger::log(
            AuditLog::CATEGORY_API,
            AuditLog::EVENT_API_TOKEN_ISSUED,
            "ออก API Token '{$request->token_name}' สำหรับ {$user->username} ({$user->name})",
            ['token_name' => $request->token_name, 'username' => $user->username],
            $user,
            null, null, null,
            $request,
        );

        app(NotificationService::class)->dispatch('api_token_issued', [
            'username' => $user->username,
            'name' => $user->name,
            'token_name' => $request->token_name,
            'ip_address' => $request->ip(),
            'description' => "ออก API Token '{$request->token_name}' สำหรับ {$user->username} ({$user->name})",
        ]);

        auth()->logout();

        return response()->json([
            'token' => $token->plainTextToken,
            'type' => 'Bearer',
        ]);
    }

    /**
     * DELETE /api/auth/token
     *
     * Revoke the currently authenticated token.
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();

        if (! $currentToken) {
            return response()->json(['message' => 'No active token'], 400);
        }

        $tokenName = $currentToken->name;
        $currentToken->delete();

        AuditLogger::log(
            AuditLog::CATEGORY_API,
            AuditLog::EVENT_API_TOKEN_REVOKED,
            "ยกเลิก API Token '{$tokenName}' ของ {$user->username}",
            ['token_name' => $tokenName, 'username' => $user->username],
            $user,
            null, null, null,
            $request,
        );

        return response()->json(['message' => 'Token revoked']);
    }

    /**
     * POST /api/auth/user-login
     *
     * Authenticate a regular UCM user (via LDAP) and return a Sanctum token
     * along with their profile and permissions for the given system.
     *
     * This allows new systems to delegate auth entirely to UCM
     * without maintaining their own user table or permission system.
     *
     * Request body:
     *   {
     *     "username": "john.doe",
     *     "password": "secret",
     *     "system":   "repair-system"   // optional — slug ของระบบที่ต้องการ permissions
     *   }
     *
     * Response:
     *   {
     *     "token": "...",
     *     "type": "Bearer",
     *     "user": { "username", "name", "email", "department", "title" },
     *     "permissions": ["view_report", "edit_order"]
     *   }
     */
    public function userLogin(Request $request, LdapService $ldap): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:200',
            'system' => 'nullable|string|max:100|exists:systems,slug',
        ]);

        // ตรวจสอบ credentials ผ่าน LDAP
        $ldapUser = $ldap->authenticate($validated['username'], $validated['password']);

        if (! $ldapUser) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // ต้องมีบัญชีใน UCM และ active
        $user = UcmUser::where('username', $ldapUser['username'])
            ->where('is_active', true)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'User not found or inactive in UCM'], 403);
        }

        // ชื่อ token = slug ของระบบ หรือ 'user-session' ถ้าไม่ระบุ
        $tokenName = $validated['system'] ?? 'user-session';

        // ลบ token เก่าของระบบนี้ก่อน (ป้องกัน token สะสม)
        $user->tokens()->where('name', $tokenName)->delete();

        // User token หมดอายุใน UCM_USER_TOKEN_TTL_HOURS ชั่วโมง (default 24)
        $ttlHours = (int) config('ucm.user_token_ttl_hours', 24);
        $expiresAt = now()->addHours($ttlHours);

        $token = $user->createToken($tokenName, ['*'], $expiresAt);

        // ดึง permissions ของระบบที่ระบุมา (ถ้ามี)
        $permissions = [];
        if (! empty($validated['system'])) {
            $system = System::where('slug', $validated['system'])
                ->where('is_active', true)
                ->first();

            if ($system) {
                $permissions = $user->getPermissionsForSystem($system->id);
            }
        }

        AuditLogger::log(
            AuditLog::CATEGORY_API,
            AuditLog::EVENT_API_USER_LOGIN,
            "User login ผ่าน API: {$user->username} ({$user->name}) ระบบ: ".($validated['system'] ?? 'ไม่ระบุ'),
            ['username' => $user->username, 'system' => $validated['system'] ?? null],
            $user,
            null, null, null,
            $request,
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => [
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department,
                'title' => $user->title,
            ],
            'permissions' => $permissions,
        ]);
    }
}
