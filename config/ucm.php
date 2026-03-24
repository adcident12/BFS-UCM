<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UCM Settings
    |--------------------------------------------------------------------------
    */

    // แผนกที่อนุญาตให้ Login (ว่าง = ทุกแผนก)
    'allowed_department' => env('UCM_ALLOWED_DEPARTMENT', ''),

    // แผนกที่ตรวจสอบ Audit
    'audit_departments' => env('UCM_AUDIT_DEPARTMENTS', ''),

    /*
    |--------------------------------------------------------------------------
    | API Token TTL
    |--------------------------------------------------------------------------
    | User token (จาก POST /api/auth/user-login) หมดอายุหลังจากจำนวนชั่วโมงนี้
    | Admin token (จาก POST /api/auth/token) ไม่มีวันหมดอายุ
    */
    'user_token_ttl_hours' => (int) env('UCM_USER_TOKEN_TTL_HOURS', 24),

];
