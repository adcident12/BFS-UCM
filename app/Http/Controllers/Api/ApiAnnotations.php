<?php

/**
 * UCM API — OpenAPI Annotations (swagger-php)
 * ─────────────────────────────────────────────────────────────────────────────
 * !! อัปเดตไฟล์นี้ทุกครั้งที่ !!
 *   - เพิ่ม endpoint ใหม่ใน routes/api.php  → เพิ่ม #[OA\Post]/#[OA\Get] ที่นี่
 *   - แก้ไข request / response ของ endpoint → แก้ annotation ที่เกี่ยวข้อง
 *   - ลบ endpoint ออก                       → ลบ annotation ออกด้วย
 *
 * หลังแก้ไขให้ run: php artisan l5-swagger:generate
 * (หรือตั้ง SWAGGER_GENERATE_ALWAYS=true ใน .env เพื่อ auto-generate)
 * ─────────────────────────────────────────────────────────────────────────────
 */

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '2.0.0',
    title: 'UCM — User Centralized Management API',
    description: "API สำหรับระบบภายนอกที่ต้องการใช้ UCM เป็น Authentication & Permission Hub\n\n## วิธีการ Authenticate\n\n### 1. OAuth 2.0 / OIDC (แนะนำ — SSO)\n- **Authorization Code + PKCE**: สำหรับ web app / SPA ที่มีผู้ใช้ login\n- **Client Credentials**: สำหรับ M2M / server-to-server\n- รับ `access_token` (RS256 JWT) มาใช้เป็น `Bearer` header\n\n### 2. Sanctum Token (legacy)\n- POST /api/auth/user-login → รับ Sanctum token\n- POST /api/auth/token → admin token (server-to-server)\n\n## OIDC Discovery\nดู endpoint metadata ทั้งหมดได้ที่ `GET /.well-known/openid-configuration`",
    contact: new OA\Contact(name: 'UCM Admin'),
)]
#[OA\Server(url: '/user-centralized-managment/api', description: 'UCM API Server')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: 'Sanctum Bearer Token — รับได้จาก POST /api/auth/token หรือ POST /api/auth/user-login',
)]
#[OA\SecurityScheme(
    securityScheme: 'oauthBearer',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'RS256 JWT',
    description: 'OAuth 2.0 Access Token (RS256 JWT) — รับได้จาก POST /api/oauth/token',
)]
#[OA\Tag(name: 'Authentication', description: 'Login, logout และออก Sanctum token (legacy)')]
#[OA\Tag(name: 'OAuth 2.0', description: 'Token endpoint สำหรับ OAuth 2.0 / OIDC')]
#[OA\Tag(name: 'Me (OAuth)', description: 'ข้อมูลผู้ใช้ที่ authenticated ด้วย OAuth Bearer token')]
#[OA\Tag(name: 'Permissions', description: 'ตรวจสอบและดึง permissions ของผู้ใช้')]
#[OA\Tag(name: 'Export', description: 'ส่งออกข้อมูลผู้ใช้ + permissions ทุกระบบ')]
class ApiAnnotations
{
    // ── Reusable Schemas (บน class ได้) ────────────────────────────────────

    #[OA\Schema(
        schema: 'UserProfile',
        type: 'object',
        properties: [
            new OA\Property(property: 'username', type: 'string', example: 'john.doe'),
            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@company.com'),
            new OA\Property(property: 'department', type: 'string', example: 'IT', nullable: true),
            new OA\Property(property: 'title', type: 'string', example: 'Developer', nullable: true),
        ],
    )]
    #[OA\Schema(
        schema: 'ErrorResponse',
        type: 'object',
        properties: [
            new OA\Property(property: 'message', type: 'string', example: 'Invalid credentials'),
        ],
    )]
    #[OA\Schema(
        schema: 'ValidationError',
        type: 'object',
        properties: [
            new OA\Property(property: 'message', type: 'string', example: 'The username field is required.'),
            new OA\Property(property: 'errors', type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string'))),
        ],
    )]

    // ── POST /auth/user-login ───────────────────────────────────────────────

    #[OA\Post(
        path: '/auth/user-login',
        operationId: 'userLogin',
        summary: 'User Login',
        description: "Authenticate ผู้ใช้ทั่วไปด้วย LDAP credentials แล้วรับ Sanctum token + permissions\n\n**Token Expiry:** 24 ชั่วโมง (ปรับได้ด้วย `UCM_USER_TOKEN_TTL_HOURS` ใน `.env`)\n\n**Rate Limit:** 10 requests / minute per IP",
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'john.doe', description: 'AD username', maxLength: 100),
                    new OA\Property(property: 'password', type: 'string', example: 'secret', format: 'password', maxLength: 200),
                    new OA\Property(property: 'system', type: 'string', example: 'repair-system', description: 'Slug ของระบบ (optional)', maxLength: 100, nullable: true),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login สำเร็จ',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'token', type: 'string', example: '1|AbCdEfGhIjKl...'),
                    new OA\Property(property: 'type', type: 'string', example: 'Bearer'),
                    new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', example: '2026-03-25T10:00:00+07:00', description: 'เวลาหมดอายุของ token (ISO 8601)'),
                    new OA\Property(property: 'user', ref: '#/components/schemas/UserProfile'),
                    new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), example: ['view_report', 'edit_order']),
                ]),
            ),
            new OA\Response(response: 401, description: 'Credentials ผิด', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'ไม่มีบัญชีใน UCM หรือ account ถูก deactivate', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Validation ผิดพลาด', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 429, description: 'Rate limit เกิน', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function userLogin(): void {}

    // ── POST /auth/token (admin) ────────────────────────────────────────────

    #[OA\Post(
        path: '/auth/token',
        operationId: 'issueAdminToken',
        summary: 'Admin Token (server-to-server)',
        description: "ออก long-lived token ด้วย admin LDAP credentials สำหรับระบบ legacy\n\nToken ไม่หมดอายุ — ยกเลิกด้วย DELETE /auth/token",
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password', 'token_name'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'admin'),
                    new OA\Property(property: 'password', type: 'string', example: 'secret', format: 'password'),
                    new OA\Property(property: 'token_name', type: 'string', example: 'repair-system', description: 'ชื่อ token (unique — ออกซ้ำแทนที่ของเดิม)', maxLength: 100),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token ออกสำเร็จ',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'token', type: 'string', example: '2|XyZaBcDeFgHi...'),
                    new OA\Property(property: 'type', type: 'string', example: 'Bearer'),
                ]),
            ),
            new OA\Response(response: 401, description: 'Credentials ผิด', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Account ไม่ใช่ admin', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function issueAdminToken(): void {}

    // ── DELETE /auth/token ──────────────────────────────────────────────────

    #[OA\Delete(
        path: '/auth/token',
        operationId: 'revokeToken',
        summary: 'Logout / Revoke Token',
        description: 'ลบ token ที่ใช้ส่งมาทิ้ง ใช้ได้ทั้ง admin token และ user token',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Token ถูกยกเลิกแล้ว', content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string', example: 'Token revoked')])),
            new OA\Response(response: 400, description: 'ไม่พบ active token ใน request นี้', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 401, description: 'ไม่มี token', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function revokeToken(): void {}

    // ── POST /permissions/check ─────────────────────────────────────────────

    #[OA\Post(
        path: '/permissions/check',
        operationId: 'checkPermission',
        summary: 'ตรวจสอบ Permission (real-time)',
        description: "เช็คว่า user มี permission key ใดๆ ในระบบที่ระบุ เหมาะสำหรับใช้ใน middleware\n\n**Token Scope:**\n- Admin token (จาก `/auth/token`) — query ได้ทุก user\n- User token (จาก `/auth/user-login`) — query ได้เฉพาะ username ของตัวเอง",
        security: [['bearerAuth' => []]],
        tags: ['Permissions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'system', 'permission'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'john.doe', maxLength: 100),
                    new OA\Property(property: 'system', type: 'string', example: 'repair-system', description: 'Slug ของระบบ', maxLength: 100),
                    new OA\Property(property: 'permission', type: 'string', example: 'approve', description: 'Permission key', maxLength: 100),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'ผลการตรวจสอบ (200 เสมอ — ดู allowed field)',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'allowed', type: 'boolean', example: true),
                    new OA\Property(property: 'reason', type: 'string', example: 'user_not_found', nullable: true),
                ]),
            ),
            new OA\Response(response: 401, description: 'ไม่มี token', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'User token พยายาม query ข้อมูลของผู้ใช้คนอื่น', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function checkPermission(): void {}

    // ── GET /users/{username}/permissions ──────────────────────────────────

    #[OA\Get(
        path: '/users/{username}/permissions',
        operationId: 'getUserPermissions',
        summary: 'ดึง permissions ของ user ในระบบที่ระบุ',
        description: "คืน array ของ permission keys ทั้งหมดที่ user มีในระบบนั้น\n\n**Token Scope:**\n- Admin token (จาก `/auth/token`) — query ได้ทุก user\n- User token (จาก `/auth/user-login`) — query ได้เฉพาะ username ของตัวเอง",
        security: [['bearerAuth' => []]],
        tags: ['Permissions'],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, description: 'UCM username', schema: new OA\Schema(type: 'string', example: 'john.doe')),
            new OA\Parameter(name: 'system', in: 'query', required: true, description: 'Slug ของระบบ', schema: new OA\Schema(type: 'string', example: 'repair-system')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permissions ของ user',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'john.doe'),
                    new OA\Property(property: 'system', type: 'string', example: 'repair-system'),
                    new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), example: ['view_report', 'edit_order']),
                ]),
            ),
            new OA\Response(response: 401, description: 'ไม่มี token', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'User token พยายาม query ข้อมูลของผู้ใช้คนอื่น', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'ไม่พบ user หรือ system', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function getUserPermissions(): void {}

    // ── GET /users/{username}/permissions/all ───────────────────────────────

    #[OA\Get(
        path: '/users/{username}/permissions/all',
        operationId: 'getAllSystemsPermissions',
        summary: 'ดึง permissions ของ user ในทุกระบบ',
        description: "คืน permissions แบบ group by system slug ในคำสั่งเดียว (single JOIN query — ไม่มี N+1)\n\n**Token Scope:**\n- Admin token (จาก `/auth/token`) — query ได้ทุก user\n- User token (จาก `/auth/user-login`) — query ได้เฉพาะ username ของตัวเอง",
        security: [['bearerAuth' => []]],
        tags: ['Permissions'],
        parameters: [
            new OA\Parameter(name: 'username', in: 'path', required: true, description: 'UCM username', schema: new OA\Schema(type: 'string', example: 'john.doe')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permissions ของ user ในทุกระบบที่ active',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'john.doe'),
                    new OA\Property(
                        property: 'systems',
                        type: 'object',
                        additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')),
                        example: ['repair-system' => ['view_report', 'edit_order'], 'hr-system' => ['view_employee']],
                    ),
                ]),
            ),
            new OA\Response(response: 401, description: 'ไม่มี token', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'User token พยายาม query ข้อมูลของผู้ใช้คนอื่น', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'ไม่พบ user', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function getAllPermissions(): void {}

    // ── GET /users/export ───────────────────────────────────────────────────

    #[OA\Get(
        path: '/users/export',
        operationId: 'exportUsers',
        summary: 'ส่งออกข้อมูลผู้ใช้ + permissions ทุกระบบ',
        description: "ดึงข้อมูลผู้ใช้ (username, employee_number, name, email, department, title) พร้อม permissions ทุกระบบที่ active\n\n- ถ้าไม่ส่ง filter จะดึงผู้ใช้ทั้งหมด\n- กรองด้วย `user_ids[]` หรือ `usernames[]` ได้ (เลือกอย่างใดอย่างหนึ่ง)\n- permissions จัด group ตาม system slug",
        security: [['bearerAuth' => []]],
        tags: ['Export'],
        parameters: [
            new OA\Parameter(
                name: 'user_ids[]',
                in: 'query',
                required: false,
                description: 'กรอง user ด้วย UCM user ID (ส่งได้หลายค่า)',
                schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')),
                example: [1, 2, 3],
            ),
            new OA\Parameter(
                name: 'usernames[]',
                in: 'query',
                required: false,
                description: 'กรอง user ด้วย username (ใช้แทน user_ids ได้)',
                schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')),
                example: ['john.doe', 'jane.smith'],
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'ข้อมูลผู้ใช้ + permissions',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'count', type: 'integer', example: 2),
                    new OA\Property(property: 'exported', type: 'string', format: 'date-time', example: '2026-03-21T10:00:00+07:00'),
                    new OA\Property(
                        property: 'users',
                        type: 'array',
                        items: new OA\Items(properties: [
                            new OA\Property(property: 'username', type: 'string', example: 'john.doe'),
                            new OA\Property(property: 'employee_number', type: 'string', example: 'EMP001', nullable: true),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', example: 'john@company.com', nullable: true),
                            new OA\Property(property: 'department', type: 'string', example: 'IT', nullable: true),
                            new OA\Property(property: 'title', type: 'string', example: 'Developer', nullable: true),
                            new OA\Property(
                                property: 'systems',
                                type: 'object',
                                description: 'permissions จัด group ตาม system slug',
                                additionalProperties: new OA\AdditionalProperties(
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                ),
                                example: ['repair-system' => ['view_report', 'edit_order'], 'hr-system' => ['view_employee']],
                            ),
                        ]),
                    ),
                ]),
            ),
            new OA\Response(response: 401, description: 'ไม่มี token', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function exportUsers(): void {}

    // ── POST /oauth/token ───────────────────────────────────────────────────

    #[OA\Post(
        path: '/oauth/token',
        operationId: 'oauthToken',
        summary: 'Token Endpoint (OAuth 2.0)',
        description: "ออก access_token / refresh_token ด้วย 3 grant types:\n\n**authorization_code** — แลก authorization code เป็น tokens (ต้องส่ง code_verifier ถ้าใช้ PKCE)\n\n**refresh_token** — ต่ออายุ token (rotation: refresh token เก่าถูก revoke)\n\n**client_credentials** — M2M token (ต้องใช้ confidential client)\n\n**Rate Limit:** 60 requests / minute",
        tags: ['OAuth 2.0'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['grant_type'],
                    properties: [
                        new OA\Property(property: 'grant_type', type: 'string', enum: ['authorization_code', 'refresh_token', 'client_credentials'], example: 'authorization_code'),
                        new OA\Property(property: 'client_id', type: 'string', example: 'ucm_AbCdEf...', description: 'ส่งใน body หรือ HTTP Basic auth'),
                        new OA\Property(property: 'client_secret', type: 'string', example: 'secret...', description: 'สำหรับ confidential client'),
                        new OA\Property(property: 'code', type: 'string', description: 'authorization_code grant'),
                        new OA\Property(property: 'redirect_uri', type: 'string', format: 'uri', description: 'authorization_code grant'),
                        new OA\Property(property: 'code_verifier', type: 'string', description: 'PKCE verifier (authorization_code + PKCE)'),
                        new OA\Property(property: 'refresh_token', type: 'string', description: 'refresh_token grant'),
                        new OA\Property(property: 'scope', type: 'string', example: 'openid profile', description: 'client_credentials grant'),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token ออกสำเร็จ',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'access_token', type: 'string', example: 'eyJhbGci...'),
                    new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                    new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                    new OA\Property(property: 'refresh_token', type: 'string', example: 'XyZaBcDe...', nullable: true),
                    new OA\Property(property: 'id_token', type: 'string', example: 'eyJhbGci...', nullable: true, description: 'ส่งเมื่อ scope มี openid'),
                    new OA\Property(property: 'scope', type: 'string', example: 'openid profile'),
                ]),
            ),
            new OA\Response(response: 400, description: 'invalid_grant / invalid_client / unsupported_grant_type', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'error', type: 'string', example: 'invalid_grant'),
                new OA\Property(property: 'error_description', type: 'string'),
            ])),
        ],
    )]
    public function oauthToken(): void {}

    // ── POST /oauth/token/revoke ────────────────────────────────────────────

    #[OA\Post(
        path: '/oauth/token/revoke',
        operationId: 'oauthRevoke',
        summary: 'Revoke Token (RFC 7009)',
        description: 'ยกเลิก access_token หรือ refresh_token ส่ง client credentials ใน HTTP Basic auth หรือ body',
        tags: ['OAuth 2.0'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['token'],
                    properties: [
                        new OA\Property(property: 'token', type: 'string', description: 'access_token หรือ refresh_token ที่ต้องการยกเลิก'),
                        new OA\Property(property: 'client_id', type: 'string'),
                        new OA\Property(property: 'client_secret', type: 'string'),
                    ],
                ),
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'สำเร็จ (RFC 7009: 200 เสมอแม้ token ไม่พบ)'),
            new OA\Response(response: 401, description: 'invalid_client'),
        ],
    )]
    public function oauthRevoke(): void {}

    // ── GET /v1/me ──────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/v1/me',
        operationId: 'meShow',
        summary: 'โปรไฟล์ผู้ใช้ที่ authenticated',
        description: "คืนข้อมูลโปรไฟล์ของผู้ใช้ที่ส่ง OAuth Bearer token มา\n\n**Required scope:** `profile` หรือ `openid`",
        security: [['oauthBearer' => []]],
        tags: ['Me (OAuth)'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'ข้อมูลโปรไฟล์',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'username', type: 'string', example: 'john.doe'),
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', example: 'john@company.com', nullable: true),
                    new OA\Property(property: 'department', type: 'string', example: 'IT', nullable: true),
                    new OA\Property(property: 'title', type: 'string', example: 'Developer', nullable: true),
                    new OA\Property(property: 'employee_number', type: 'string', example: 'EMP001', nullable: true),
                    new OA\Property(property: 'is_admin', type: 'integer', example: 0),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                    new OA\Property(property: 'last_login_at', type: 'string', format: 'date-time', nullable: true),
                ]),
            ),
            new OA\Response(response: 401, description: 'unauthorized / invalid_token'),
            new OA\Response(response: 403, description: 'insufficient_scope'),
        ],
    )]
    public function meShow(): void {}

    // ── GET /v1/me/permissions ──────────────────────────────────────────────

    #[OA\Get(
        path: '/v1/me/permissions',
        operationId: 'mePermissions',
        summary: 'Permissions ของผู้ใช้ในระบบนี้',
        description: "คืนรายการ permissions ของผู้ใช้ในระบบที่ตรงกับ OAuth client slug\n\n**Required scope:** `permissions`",
        security: [['oauthBearer' => []]],
        tags: ['Me (OAuth)'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permissions',
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'system', type: 'object', properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 3),
                        new OA\Property(property: 'slug', type: 'string', example: 'repair-system'),
                        new OA\Property(property: 'name', type: 'string', example: 'Repair System'),
                    ]),
                    new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), example: ['view_report', 'approve']),
                ]),
            ),
            new OA\Response(response: 401, description: 'unauthorized / invalid_token'),
            new OA\Response(response: 403, description: 'insufficient_scope'),
        ],
    )]
    public function mePermissions(): void {}

    // ── GET /v1/me/systems ──────────────────────────────────────────────────

    #[OA\Get(
        path: '/v1/me/systems',
        operationId: 'meSystems',
        summary: 'ระบบทั้งหมดที่ผู้ใช้มีสิทธิ์',
        description: "คืนรายการระบบ (active) ที่ผู้ใช้มี permission อย่างน้อยหนึ่งรายการ พร้อมรายการ permissions\n\n**Required scope:** `profile`",
        security: [['oauthBearer' => []]],
        tags: ['Me (OAuth)'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'ระบบที่มีสิทธิ์',
                content: new OA\JsonContent(properties: [
                    new OA\Property(
                        property: 'systems',
                        type: 'array',
                        items: new OA\Items(properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 3),
                            new OA\Property(property: 'slug', type: 'string', example: 'repair-system'),
                            new OA\Property(property: 'name', type: 'string', example: 'Repair System'),
                            new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string')),
                        ]),
                    ),
                ]),
            ),
            new OA\Response(response: 401, description: 'unauthorized / invalid_token'),
            new OA\Response(response: 403, description: 'insufficient_scope'),
        ],
    )]
    public function meSystems(): void {}
}
