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
    version: '1.0.0',
    title: 'UCM — User Centralized Management API',
    description: "API สำหรับระบบภายนอกที่ต้องการใช้ UCM เป็น Authentication & Permission Hub\n\n## การทำงาน\n1. เรียก POST /api/auth/user-login เพื่อ authenticate ผู้ใช้ผ่าน LDAP\n2. UCM คืน Bearer Token + ข้อมูล user + permissions กลับมา\n3. ใช้ token สำหรับเรียก permission endpoints เพิ่มเติม\n\n## ระบบ Legacy (server-to-server)\nใช้ POST /api/auth/token ด้วย admin credentials เพื่อรับ long-lived token",
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
#[OA\Tag(name: 'Authentication', description: 'Login, logout และออก token')]
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
        description: "Authenticate ผู้ใช้ทั่วไปด้วย LDAP credentials แล้วรับ Sanctum token + permissions\n\n**Rate Limit:** 10 requests / minute per IP",
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
}
