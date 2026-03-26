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
    | Admin token (จาก POST /api/auth/token) หมดอายุหลังจากจำนวนวันนี้
    |   ตั้ง 0 = ไม่มีวันหมดอายุ (ไม่แนะนำ)
    */
    'user_token_ttl_hours' => (int) env('UCM_USER_TOKEN_TTL_HOURS', 24),

    'admin_token_ttl_days' => (int) env('UCM_ADMIN_TOKEN_TTL_DAYS', 90),

];
