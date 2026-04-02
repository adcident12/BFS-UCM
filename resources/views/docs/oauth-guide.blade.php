@extends('layouts.docs')
@section('doc-title', 'OAuth 2.0 / OIDC Integration Guide')

@php
$base = config('app.url');
$sections = [
    ['id' => 'overview',        'label' => 'ภาพรวม'],
    ['id' => 'register',        'label' => '1. ลงทะเบียน OAuth Client'],
    ['id' => 'flows',           'label' => '2. เลือก Grant Type'],
    ['id' => 'authcode',        'label' => '↳ Authorization Code + PKCE'],
    ['id' => 'client-creds',    'label' => '↳ Client Credentials (M2M)'],
    ['id' => 'refresh',         'label' => '↳ Refresh Token'],
    ['id' => 'userinfo',        'label' => '3. ดึงข้อมูลผู้ใช้'],
    ['id' => 'me-api',          'label' => '4. Resource API v1 (/me)'],
    ['id' => 'scopes',          'label' => '5. Scopes & Claims'],
    ['id' => 'revoke',          'label' => '6. Revoke Token'],
    ['id' => 'discovery',       'label' => '7. OIDC Discovery'],
    ['id' => 'php-example',     'label' => 'ตัวอย่าง PHP'],
    ['id' => 'security-notes',  'label' => 'Security Notes'],
];
@endphp

@push('doc-page-toc')
@foreach($sections as $s)
<a href="#{{ $s['id'] }}" class="toc-item">{{ $s['label'] }}</a>
@endforeach
@endpush

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl p-7"
         style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%)">
        <div class="absolute inset-0 pointer-events-none"
             style="background-image:linear-gradient(rgba(99,102,241,0.08) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,0.08) 1px,transparent 1px);background-size:28px 28px"></div>
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-indigo-600/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">OAuth 2.0 / OIDC Integration Guide</h1>
                <p class="text-slate-400 text-sm mt-1">สำหรับนักพัฒนาที่ต้องการเชื่อม UCM เป็น Identity Provider (IdP)</p>
            </div>
        </div>
    </div>

    {{-- Overview --}}
    <div id="overview" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">ภาพรวม</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3 leading-relaxed">
            <p>UCM ทำหน้าที่เป็น <strong>OAuth 2.0 Authorization Server + OIDC Provider</strong> ระบบภายนอกสามารถ:</p>
            <ul class="list-disc list-inside space-y-1 text-slate-600">
                <li>ให้ผู้ใช้ SSO ผ่าน UCM — login ครั้งเดียว ใช้ได้ทุกระบบ</li>
                <li>ดึงข้อมูลโปรไฟล์, อีเมล, แผนก, ตำแหน่ง จาก LDAP/AD</li>
                <li>ดึง permissions เฉพาะระบบผ่าน scope <code class="bg-slate-100 px-1 rounded">permissions</code></li>
                <li>ใช้ Client Credentials (M2M) สำหรับ server-to-server โดยไม่ต้องมีผู้ใช้</li>
            </ul>
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                <p class="font-semibold text-indigo-800 mb-1">Endpoints หลัก</p>
                <table class="text-xs w-full">
                    <tbody class="divide-y divide-indigo-100">
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /.well-known/openid-configuration</td><td class="py-1 text-slate-600">OIDC Discovery</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /oauth/authorize</td><td class="py-1 text-slate-600">หน้า consent / SSO redirect</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">POST /api/oauth/token</td><td class="py-1 text-slate-600">ออก / ต่ออายุ token</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">POST /api/oauth/token/revoke</td><td class="py-1 text-slate-600">ยกเลิก token (RFC 7009)</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /api/oauth/userinfo</td><td class="py-1 text-slate-600">OIDC UserInfo endpoint</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /oauth/jwks</td><td class="py-1 text-slate-600">RS256 Public Key (JWKS)</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /api/v1/me</td><td class="py-1 text-slate-600">โปรไฟล์ผู้ใช้ (OAuth Bearer)</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /api/v1/me/permissions</td><td class="py-1 text-slate-600">permissions ในระบบนี้</td></tr>
                        <tr><td class="py-1 font-mono text-indigo-700 pr-4">GET  /api/v1/me/systems</td><td class="py-1 text-slate-600">ทุกระบบที่มีสิทธิ์</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Register --}}
    <div id="register" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">1. ลงทะเบียน OAuth Client</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3 leading-relaxed">
            <p>Admin UCM ลงทะเบียน client ผ่านหน้า <strong>Admin → OAuth Clients → เพิ่ม OAuth Client</strong></p>
            <div class="bg-slate-50 rounded-xl p-4 space-y-2 text-xs">
                <div class="grid grid-cols-3 gap-2">
                    <div class="font-semibold text-slate-600">Field</div>
                    <div class="font-semibold text-slate-600 col-span-2">คำอธิบาย</div>
                    <div class="font-mono text-slate-700">name</div><div class="col-span-2 text-slate-600">ชื่อแอป (แสดงบนหน้า consent)</div>
                    <div class="font-mono text-slate-700">slug</div><div class="col-span-2 text-slate-600">ต้องตรงกับ UCM System slug เพื่อ map permissions</div>
                    <div class="font-mono text-slate-700">redirect_uris</div><div class="col-span-2 text-slate-600">URL ที่รับ authorization code กลับ (whitelist)</div>
                    <div class="font-mono text-slate-700">allowed_scopes</div><div class="col-span-2 text-slate-600">scopes ที่แอปขอได้</div>
                    <div class="font-mono text-slate-700">is_confidential</div><div class="col-span-2 text-slate-600">true = มี client_secret (server-side), false = PKCE-only (SPA/mobile)</div>
                    <div class="font-mono text-slate-700">auto_approve</div><div class="col-span-2 text-slate-600">ข้ามหน้า consent สำหรับแอปภายใน</div>
                </div>
            </div>
            <p class="text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5">
                <strong>⚠ Client Secret</strong> จะแสดงเพียงครั้งเดียวหลังสร้าง เก็บไว้ในที่ปลอดภัย (environment variable) ห้ามเขียนลงใน code
            </p>
        </div>
    </div>

    {{-- Flows --}}
    <div id="flows" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">2. เลือก Grant Type</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                    <p class="font-bold text-indigo-800 mb-1">Authorization Code + PKCE</p>
                    <p class="text-xs text-indigo-700">Web app, SPA, Mobile ที่มีผู้ใช้ login<br>ปลอดภัยที่สุด</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <p class="font-bold text-slate-700 mb-1">Client Credentials</p>
                    <p class="text-xs text-slate-600">M2M: backend ↔ UCM API<br>ไม่ต้องมีผู้ใช้ล็อกอิน</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <p class="font-bold text-slate-700 mb-1">Refresh Token</p>
                    <p class="text-xs text-slate-600">ต่ออายุ access token อัตโนมัติ<br>(token rotation — ปลอดภัย)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Authorization Code --}}
    <div id="authcode" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm">1</div>
            <h2 class="font-bold text-slate-800">Authorization Code + PKCE</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-5">

            <div>
                <p class="font-semibold text-slate-700 mb-2">Step 1 — สร้าง PKCE code_verifier และ code_challenge</p>
                <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>// PHP
$codeVerifier  = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
$codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');</code></pre>
            </div>

            <div>
                <p class="font-semibold text-slate-700 mb-2">Step 2 — Redirect ผู้ใช้ไป /oauth/authorize</p>
                <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>GET {{ $base }}/oauth/authorize?
  response_type=code
  &client_id=ucm_AbCdEfGhIjKl...
  &redirect_uri=https%3A%2F%2Fapp.example.com%2Fauth%2Fcallback
  &scope=openid+profile+permissions
  &state=RANDOM_STATE_VALUE        ← ป้องกัน CSRF (เก็บไว้ใน session)
  &code_challenge=BASE64URL_SHA256
  &code_challenge_method=S256</code></pre>
                <p class="text-xs text-slate-500 mt-2">UCM จะแสดงหน้า consent (หรือ auto-approve ถ้าตั้งไว้) แล้ว redirect กลับพร้อม <code>?code=...&state=...</code></p>
            </div>

            <div>
                <p class="font-semibold text-slate-700 mb-2">Step 3 — แลก code เป็น token</p>
                <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>POST {{ $base }}/api/oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code
&code=CODE_FROM_REDIRECT
&redirect_uri=https://app.example.com/auth/callback
&client_id=ucm_AbCdEfGhIjKl...
&client_secret=SECRET...            ← confidential client เท่านั้น
&code_verifier=CODE_VERIFIER        ← PKCE verifier

// Response
{
  "access_token":  "eyJhbGci...",   ← RS256 JWT (1 ชั่วโมง)
  "token_type":    "Bearer",
  "expires_in":    3600,
  "refresh_token": "XyZ...",         ← 30 วัน (rotated on use)
  "id_token":      "eyJhbGci...",   ← OIDC id_token (มี scope openid)
  "scope":         "openid profile permissions"
}</code></pre>
            </div>
        </div>
    </div>

    {{-- Client Credentials --}}
    <div id="client-creds" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-slate-200 rounded-xl flex items-center justify-center text-slate-600 font-bold text-sm">M2M</div>
            <h2 class="font-bold text-slate-800">Client Credentials (M2M)</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3">
            <p>ใช้สำหรับ backend-to-UCM โดยไม่ต้องมีผู้ใช้ล็อกอิน ต้องเป็น <strong>confidential client</strong></p>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>POST {{ $base }}/api/oauth/token
Authorization: Basic BASE64(client_id:client_secret)   ← แนะนำ
Content-Type: application/x-www-form-urlencoded

grant_type=client_credentials
&scope=users:read

// Response — ไม่มี refresh_token
{
  "access_token": "eyJhbGci...",
  "token_type":   "Bearer",
  "expires_in":   3600,
  "scope":        "users:read"
}</code></pre>
        </div>
    </div>

    {{-- Refresh --}}
    <div id="refresh" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-slate-200 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">Refresh Token</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3">
            <p>Token rotation: ทุกครั้งที่ refresh จะได้ <strong>access_token + refresh_token ใหม่</strong> — token เก่าถูก revoke ทันที</p>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>POST {{ $base }}/api/oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=refresh_token
&refresh_token=REFRESH_TOKEN
&client_id=ucm_AbCdEfGhIjKl...
&client_secret=SECRET...</code></pre>
        </div>
    </div>

    {{-- UserInfo --}}
    <div id="userinfo" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">3. ดึงข้อมูลผู้ใช้ (UserInfo / OIDC)</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3">
            <p>ใช้ access_token เรียก OIDC UserInfo endpoint — คืน claims ตาม scope ที่ขอไว้</p>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>GET {{ $base }}/api/oauth/userinfo
Authorization: Bearer ACCESS_TOKEN

// Response (scope: openid profile email permissions)
{
  "sub":                "42",
  "name":               "John Doe",
  "preferred_username": "john.doe",
  "email":              "john@company.com",
  "email_verified":     true,
  "department":         "IT",
  "title":              "Developer",
  "is_admin":           0,
  "permissions":        ["view_report", "approve"]  ← scope permissions
}</code></pre>
        </div>
    </div>

    {{-- Me API --}}
    <div id="me-api" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-violet-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">4. Resource API v1 (/me)</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="text-left">
                        <tr class="border-b border-slate-200">
                            <th class="pb-2 font-semibold text-slate-600 pr-4">Endpoint</th>
                            <th class="pb-2 font-semibold text-slate-600 pr-4">Required Scope</th>
                            <th class="pb-2 font-semibold text-slate-600">คืน</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 font-mono text-indigo-700 pr-4">GET /api/v1/me</td>
                            <td class="py-2 text-slate-600 pr-4">profile หรือ openid</td>
                            <td class="py-2 text-slate-600">โปรไฟล์ผู้ใช้ (id, username, name, email, ...)</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-mono text-indigo-700 pr-4">GET /api/v1/me/permissions</td>
                            <td class="py-2 text-slate-600 pr-4">permissions</td>
                            <td class="py-2 text-slate-600">permissions ใน system ที่ตรงกับ client slug</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-mono text-indigo-700 pr-4">GET /api/v1/me/systems</td>
                            <td class="py-2 text-slate-600 pr-4">profile</td>
                            <td class="py-2 text-slate-600">ทุกระบบที่มีสิทธิ์ + permission list</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>GET {{ $base }}/api/v1/me/permissions
Authorization: Bearer ACCESS_TOKEN

// Response
{
  "system":      { "id": 3, "slug": "repair-system", "name": "Repair System" },
  "permissions": ["view_report", "approve", "edit_order"]
}</code></pre>
        </div>
    </div>

    {{-- Scopes --}}
    <div id="scopes" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">5. Scopes & Claims</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700">
            <table class="w-full text-xs">
                <thead class="text-left">
                    <tr class="border-b border-slate-200">
                        <th class="pb-2 font-semibold text-slate-600 pr-4">Scope</th>
                        <th class="pb-2 font-semibold text-slate-600 pr-4">Claims ที่ได้</th>
                        <th class="pb-2 font-semibold text-slate-600">ใช้กับ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-2 font-mono text-indigo-700 pr-4">openid</td>
                        <td class="py-2 text-slate-600 pr-4">sub, iss, aud, exp, iat, nonce</td>
                        <td class="py-2 text-slate-600">ต้องมีเสมอสำหรับ OIDC — ออก id_token</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-mono text-indigo-700 pr-4">profile</td>
                        <td class="py-2 text-slate-600 pr-4">name, preferred_username, department, title, is_admin</td>
                        <td class="py-2 text-slate-600">ดึงข้อมูลโปรไฟล์</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-mono text-indigo-700 pr-4">email</td>
                        <td class="py-2 text-slate-600 pr-4">email, email_verified</td>
                        <td class="py-2 text-slate-600">ดึงอีเมล</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-mono text-indigo-700 pr-4">permissions</td>
                        <td class="py-2 text-slate-600 pr-4">permissions (array ของ permission keys)</td>
                        <td class="py-2 text-slate-600">ตรวจ permissions ใน system นั้น</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-mono text-indigo-700 pr-4">users:read</td>
                        <td class="py-2 text-slate-600 pr-4">(ใช้กับ M2M token)</td>
                        <td class="py-2 text-slate-600">อ่านรายชื่อพนักงานทั้งหมด</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Revoke --}}
    <div id="revoke" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">6. Revoke Token</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3">
            <p>ยกเลิก access_token หรือ refresh_token (RFC 7009 — ตอบ 200 เสมอ)</p>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>POST {{ $base }}/api/oauth/token/revoke
Content-Type: application/x-www-form-urlencoded
Authorization: Basic BASE64(client_id:client_secret)

token=ACCESS_OR_REFRESH_TOKEN</code></pre>
        </div>
    </div>

    {{-- Discovery --}}
    <div id="discovery" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-slate-200 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">7. OIDC Discovery</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700 space-y-3">
            <p>Library ส่วนใหญ่ค้นหา endpoint อัตโนมัติจาก discovery document:</p>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>GET {{ $base }}/.well-known/openid-configuration

// ตัวอย่าง response
{
  "issuer":                 "{{ $base }}",
  "authorization_endpoint": "{{ $base }}/oauth/authorize",
  "token_endpoint":         "{{ $base }}/api/oauth/token",
  "userinfo_endpoint":      "{{ $base }}/api/oauth/userinfo",
  "jwks_uri":               "{{ $base }}/oauth/jwks",
  "scopes_supported":       ["openid","profile","email","permissions","users:read"],
  "id_token_signing_alg_values_supported": ["RS256"],
  "code_challenge_methods_supported": ["S256","plain"]
}</code></pre>
            <p class="text-xs text-slate-500">JWKS (RS256 public key) สำหรับ verify id_token: <code class="bg-slate-100 px-1.5 rounded">GET {{ $base }}/oauth/jwks</code></p>
        </div>
    </div>

    {{-- PHP Example --}}
    <div id="php-example" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">ตัวอย่าง PHP — Authorization Code + PKCE</h2>
        </div>
        <div class="px-6 py-5 text-sm">
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed"><code>&lt;?php
// ─── config ───────────────────────────────────────────────────────────
$UCM_BASE       = '{{ $base }}';
$CLIENT_ID      = getenv('UCM_CLIENT_ID');
$CLIENT_SECRET  = getenv('UCM_CLIENT_SECRET');   // null สำหรับ public client
$REDIRECT_URI   = 'https://yourapp.com/auth/callback';
$SCOPES         = 'openid profile permissions';

// ─── Step 1: สร้าง PKCE ─────────────────────────────────────────────
session_start();
$verifier   = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
$challenge  = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
$state      = bin2hex(random_bytes(16));
$_SESSION['pkce_verifier'] = $verifier;
$_SESSION['oauth_state']   = $state;

// ─── Step 2: Redirect ────────────────────────────────────────────────
$params = http_build_query([
    'response_type'         => 'code',
    'client_id'             => $CLIENT_ID,
    'redirect_uri'          => $REDIRECT_URI,
    'scope'                 => $SCOPES,
    'state'                 => $state,
    'code_challenge'        => $challenge,
    'code_challenge_method' => 'S256',
]);
header("Location: {$UCM_BASE}/oauth/authorize?{$params}");
exit;

// ─── Callback: แลก code เป็น token ──────────────────────────────────
// (ใน /auth/callback)
session_start();
if ($_GET['state'] !== $_SESSION['oauth_state']) {
    die('State mismatch — possible CSRF');
}

$response = json_decode(file_get_contents("{$UCM_BASE}/api/oauth/token", false,
    stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query([
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code'],
            'redirect_uri'  => $REDIRECT_URI,
            'client_id'     => $CLIENT_ID,
            'client_secret' => $CLIENT_SECRET,
            'code_verifier' => $_SESSION['pkce_verifier'],
        ]),
    ]])
), true);

$_SESSION['access_token']  = $response['access_token'];
$_SESSION['refresh_token'] = $response['refresh_token'];

// ─── ดึงข้อมูลผู้ใช้ ─────────────────────────────────────────────────
$user = json_decode(file_get_contents("{$UCM_BASE}/api/v1/me", false,
    stream_context_create(['http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$_SESSION['access_token']}\r\n",
    ]])
), true);

echo "สวัสดี {$user['name']} ({$user['username']})";
</code></pre>
        </div>
    </div>

    {{-- Security Notes --}}
    <div id="security-notes" class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h2 class="font-bold text-slate-800">Security Notes</h2>
        </div>
        <div class="px-6 py-5 text-sm text-slate-700">
            <ul class="space-y-2">
                <li class="flex items-start gap-2"><span class="text-emerald-500 font-bold mt-0.5">✓</span><span>ใช้ PKCE (<code class="bg-slate-100 px-1 rounded">S256</code>) เสมอ แม้ใน confidential client</span></li>
                <li class="flex items-start gap-2"><span class="text-emerald-500 font-bold mt-0.5">✓</span><span>ตรวจสอบ <code class="bg-slate-100 px-1 rounded">state</code> parameter ทุกครั้งในหน้า callback (ป้องกัน CSRF)</span></li>
                <li class="flex items-start gap-2"><span class="text-emerald-500 font-bold mt-0.5">✓</span><span>เก็บ <code class="bg-slate-100 px-1 rounded">client_secret</code> ใน environment variable ห้ามเขียนลงใน code หรือ repository</span></li>
                <li class="flex items-start gap-2"><span class="text-emerald-500 font-bold mt-0.5">✓</span><span>Access token อายุ 1 ชั่วโมง ให้ใช้ refresh token ต่ออายุอัตโนมัติ</span></li>
                <li class="flex items-start gap-2"><span class="text-emerald-500 font-bold mt-0.5">✓</span><span>Verify <code class="bg-slate-100 px-1 rounded">id_token</code> ด้วย RS256 public key จาก <code class="bg-slate-100 px-1 rounded">/oauth/jwks</code> ก่อนเชื่อถือ claims</span></li>
                <li class="flex items-start gap-2"><span class="text-emerald-500 font-bold mt-0.5">✓</span><span>เรียก revoke token เมื่อ logout เพื่อให้ token ใช้ไม่ได้ทันที</span></li>
                <li class="flex items-start gap-2"><span class="text-red-500 font-bold mt-0.5">✗</span><span>อย่าเก็บ access_token ใน <code class="bg-slate-100 px-1 rounded">localStorage</code> — เสี่ยง XSS ใช้ httpOnly cookie หรือ server-side session แทน</span></li>
            </ul>
        </div>
    </div>

</div>
@endsection
