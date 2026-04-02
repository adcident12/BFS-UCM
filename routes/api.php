<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OAuthController;
use App\Http\Controllers\Api\OidcController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserExportController;
use App\Http\Controllers\Api\V1\MeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| UCM API Routes
|--------------------------------------------------------------------------
|
| These routes are called by legacy systems to:
|   1. Obtain a Sanctum token (via admin LDAP credentials)
|   2. Query user permissions managed in UCM
|
| Authentication: Bearer token (Laravel Sanctum)
|
| Example usage from a legacy system (PHP):
|
|   // Step 1: Get token once (store it, reuse until revoked)
|   $res = Http::post('https://bfscn8n.bfsasia.com/user-centralized-managment/api/auth/token', [
|       'username'   => 'admin',
|       'password'   => 'secret',
|       'token_name' => 'repair-system',
|   ]);
|   $token = $res->json('token');
|
|   // Step 2: Check permission
|   $res = Http::withToken($token)
|       ->post('.../api/permissions/check', [
|           'username'   => 'john.doe',
|           'system'     => 'repair-system',
|           'permission' => 'role_admin',
|       ]);
|   $allowed = $res->json('allowed'); // true / false
|
*/

// Public: issue admin token (LDAP admin credentials required, rate limited: 10 attempts/minute)
Route::post('/auth/token', [AuthController::class, 'issueToken'])->middleware('throttle:10,1');

// Public: user login — returns token + user info + permissions (rate limited: 10 attempts/minute)
Route::post('/auth/user-login', [AuthController::class, 'userLogin'])->middleware('throttle:10,1');

// Protected by Sanctum token
Route::middleware('auth:sanctum')->group(function () {

    // Revoke current token
    Route::delete('/auth/token', [AuthController::class, 'revokeToken']);

    // Permission queries
    Route::get('/users/{username}/permissions', [PermissionController::class, 'forUser']);
    Route::get('/users/{username}/permissions/all', [PermissionController::class, 'allSystems']);
    Route::post('/permissions/check', [PermissionController::class, 'check']);

    // User export
    Route::get('/users/export', [UserExportController::class, 'export']);
});

// ── OAuth 2.0 Token Endpoints (no session cookie — stateless) ─────────────────
// Token issuance (auth_code / refresh_token / client_credentials)
Route::post('/oauth/token', [OAuthController::class, 'token'])->middleware('throttle:60,1');
// Token revocation (RFC 7009)
Route::post('/oauth/token/revoke', [OAuthController::class, 'revoke']);

// OIDC UserInfo endpoint
Route::get('/oauth/userinfo', [OidcController::class, 'userinfo']);

// ── UCM Resource API v1 ───────────────────────────────────────────────────────
// All routes validated by Bearer token inside each controller via OAuthService
Route::prefix('v1')->group(function (): void {
    Route::get('/me', [MeController::class, 'show']);
    Route::get('/me/permissions', [MeController::class, 'permissions']);
    Route::get('/me/systems', [MeController::class, 'systems']);
});
