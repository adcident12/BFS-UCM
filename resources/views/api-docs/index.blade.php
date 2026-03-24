{{--
 | UCM API Documentation
 |─────────────────────────────────────────────────────────────────────────────
 | !! อัปเดตไฟล์นี้ทุกครั้งที่ !!
 |   - เพิ่ม endpoint ใหม่ใน routes/api.php
 |   - แก้ไข request / response ของ endpoint เดิม
 |   - ลบ endpoint ออก
 |
 | Endpoint ทั้งหมดอยู่ใน routes/api.php (prefix: /api)
 | Controller: app/Http/Controllers/Api/AuthController.php
 |             app/Http/Controllers/Api/PermissionController.php
--}}
@extends('layouts.app')
@section('title', 'API Documentation')
@section('header', 'API Documentation')

@section('content')

{{-- Overview --}}
<div class="mb-6 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-lg shadow-indigo-200">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold bg-white/20 px-2.5 py-1 rounded-full tracking-wide">v1.0</span>
                <span class="text-xs text-indigo-200 font-medium">UCM REST API</span>
            </div>
            <h2 class="text-xl font-bold leading-tight">User Centralized Management API</h2>
            <p class="text-indigo-200 text-sm mt-1">ให้ระบบภายนอก authenticate ผู้ใช้และตรวจสอบ permissions ผ่าน UCM โดยไม่ต้องมี auth เป็นของตัวเอง</p>
        </div>
        <div class="flex-shrink-0 bg-white/10 rounded-xl px-4 py-3 font-mono text-sm text-indigo-100 border border-white/20">
            <div class="text-xs text-indigo-300 mb-1 font-sans font-semibold">Base URL</div>
            {{ url('/api') }}
        </div>
    </div>
</div>

{{-- Auth note --}}
<div class="mb-6 bg-amber-50 border border-amber-200/80 rounded-2xl px-5 py-4 flex gap-3 text-sm">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
    </svg>
    <div>
        <p class="font-semibold text-amber-800 mb-1">Authentication — Bearer Token (Laravel Sanctum)</p>
        <p class="text-amber-700">Endpoint ที่ต้องการ auth ให้ส่ง header: <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono text-xs">Authorization: Bearer &lt;token&gt;</code></p>
        <p class="text-amber-700 mt-0.5">รับ token ได้จาก <span class="font-semibold">POST /api/auth/token</span> (admin) หรือ <span class="font-semibold">POST /api/auth/user-login</span> (user ทั่วไป)</p>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Group: Authentication --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<x-api-group label="Authentication" color="indigo">

    {{-- POST /api/auth/user-login --}}
    <x-api-endpoint
        method="POST"
        path="/api/auth/user-login"
        summary="User Login — รับ token + ข้อมูล user + permissions"
        auth="false"
        rate-limit="10 requests / minute"
        description="ให้ผู้ใช้ทั่วไป login ด้วย username/password (ตรวจสอบผ่าน LDAP) แล้วรับ Sanctum token พร้อม permissions ของระบบที่ระบุ ใช้สำหรับระบบใหม่ที่ต้องการ delegate auth ทั้งหมดมาที่ UCM — Token มีอายุ 24 ชั่วโมง (ปรับได้ด้วย UCM_USER_TOKEN_TTL_HOURS)"
    >
        <x-slot name="requestBody">
{
  "username":   "john.doe",        // required — AD username
  "password":   "secret",          // required — AD password
  "system":     "repair-system"    // optional — slug ของระบบ (รับ permissions คืนมาด้วย)
}
        </x-slot>

        <x-slot name="responses">
// 200 OK
{
  "token":       "1|AbCdEf...",
  "type":        "Bearer",
  "expires_at":  "2026-03-25T10:00:00+07:00",   // หมดอายุใน 24 ชม.
  "user": {
    "username":   "john.doe",
    "name":       "John Doe",
    "email":      "john@company.com",
    "department": "IT",
    "title":      "Developer"
  },
  "permissions": ["view_report", "edit_order", "approve"]
}

// 401 — password ผิด หรือ ไม่พบใน AD
{ "message": "Invalid credentials" }

// 403 — ไม่มีบัญชีใน UCM หรือ inactive
{ "message": "User not found or inactive in UCM" }

// 422 — validation ผิดพลาด
{ "message": "The username field is required.", "errors": {...} }

// 429 — rate limit เกิน
{ "message": "Too Many Attempts." }
        </x-slot>

        <x-slot name="example">
// PHP (Laravel Http)
$res = Http::post('{{ url("/api/auth/user-login") }}', [
    'username' => 'john.doe',
    'password' => 'secret',
    'system'   => 'repair-system',
]);
$token      = $res->json('token');
$expiresAt  = $res->json('expires_at'); // "2026-03-25T10:00:00+07:00"
$permissions = $res->json('permissions'); // ["view_report", ...]

// JavaScript (fetch)
const res = await fetch('{{ url("/api/auth/user-login") }}', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify({ username: 'john.doe', password: 'secret', system: 'repair-system' }),
});
const { token, expires_at, user, permissions } = await res.json();
        </x-slot>
    </x-api-endpoint>

    {{-- DELETE /api/auth/token (user logout) --}}
    <x-api-endpoint
        method="DELETE"
        path="/api/auth/token"
        summary="Logout — ยกเลิก token ปัจจุบัน"
        auth="true"
        description="ลบ token ที่ใช้ส่งมาทิ้ง (ใช้ได้ทั้ง admin token และ user token)"
    >
        <x-slot name="requestBody">
// ไม่มี request body — ใช้ token ใน Authorization header
        </x-slot>

        <x-slot name="responses">
// 200 OK
{ "message": "Token revoked" }

// 400 — ไม่พบ active token ที่ผูกกับ request
{ "message": "No active token" }

// 401 — ไม่มี / หมดอายุ token
{ "message": "Unauthenticated." }
        </x-slot>

        <x-slot name="example">
// PHP (Laravel Http)
Http::withToken($token)->delete('{{ url("/api/auth/token") }}');

// JavaScript (fetch)
await fetch('{{ url("/api/auth/token") }}', {
    method:  'DELETE',
    headers: { 'Authorization': 'Bearer ' + token },
});
        </x-slot>
    </x-api-endpoint>

    {{-- POST /api/auth/token (admin) --}}
    <x-api-endpoint
        method="POST"
        path="/api/auth/token"
        summary="Admin Token — สำหรับระบบ Legacy ที่ต้องการ query permissions"
        auth="false"
        description="ออก token ด้วย AD admin account สำหรับระบบ legacy ที่ต้องการเรียก permission API โดยไม่มี user login flow (server-to-server) token นี้ไม่หมดอายุ — ยกเลิกด้วย DELETE /api/auth/token"
    >
        <x-slot name="requestBody">
{
  "username":   "admin",           // required — AD admin username
  "password":   "secret",          // required — AD admin password
  "token_name": "repair-system"    // required — ชื่อ token (1 ชื่อ = 1 token, ออกซ้ำจะแทนที่ของเดิม)
}
        </x-slot>

        <x-slot name="responses">
// 200 OK
{ "token": "2|XyZaBc...", "type": "Bearer" }

// 401 — credentials ผิด
{ "message": "Invalid credentials" }

// 403 — account ไม่ใช่ admin
{ "message": "Forbidden: admin account required" }
        </x-slot>

        <x-slot name="example">
// PHP — เรียกครั้งแรกเพื่อเก็บ token (เก็บใน config / env)
$res = Http::post('{{ url("/api/auth/token") }}', [
    'username'   => 'admin',
    'password'   => env('UCM_ADMIN_PASSWORD'),
    'token_name' => 'repair-system',
]);
$token = $res->json('token');
// จากนั้นเก็บ $token ไว้ใช้ต่อ ไม่ต้อง login ซ้ำ
        </x-slot>
    </x-api-endpoint>

</x-api-group>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Group: Permissions --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<x-api-group label="Permissions" color="violet">

    {{-- POST /api/permissions/check --}}
    <x-api-endpoint
        method="POST"
        path="/api/permissions/check"
        summary="ตรวจสอบว่า user มี permission นี้ไหม"
        auth="true"
        description="เช็คแบบ real-time ว่า user มี permission key ใดๆ ในระบบที่ระบุ เหมาะสำหรับ middleware หรือ guard ในระบบภายนอก — Admin token ตรวจสอบได้ทุก user, User token ตรวจสอบได้เฉพาะของตัวเอง"
    >
        <x-slot name="requestBody">
{
  "username":   "john.doe",        // required — UCM username
  "system":     "repair-system",   // required — slug ของระบบ
  "permission": "approve"          // required — permission key ที่ต้องการเช็ค
}
        </x-slot>

        <x-slot name="responses">
// 200 — มี permission
{ "allowed": true }

// 200 — ไม่มี permission หรือ user ไม่พบ
{ "allowed": false }

// 200 — user ไม่พบใน UCM
{ "allowed": false, "reason": "user_not_found" }

// 403 — User token พยายาม query ผู้ใช้คนอื่น
{ "message": "Forbidden" }
        </x-slot>

        <x-slot name="example">
// PHP — ใช้ใน middleware ของระบบ legacy
$res = Http::withToken($adminToken)
    ->post('{{ url("/api/permissions/check") }}', [
        'username'   => auth()->user()->username,
        'system'     => 'repair-system',
        'permission' => 'approve_repair',
    ]);

if (! $res->json('allowed')) {
    abort(403);
}

// JavaScript
const res = await fetch('{{ url("/api/permissions/check") }}', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + adminToken },
    body:    JSON.stringify({ username: 'john.doe', system: 'repair-system', permission: 'approve' }),
});
const { allowed } = await res.json();
        </x-slot>
    </x-api-endpoint>

    {{-- GET /api/users/{username}/permissions --}}
    <x-api-endpoint
        method="GET"
        path="/api/users/{username}/permissions"
        summary="ดึง permissions ของ user ในระบบที่ระบุ"
        auth="true"
        description="คืน array ของ permission keys ทั้งหมดที่ user มีในระบบนั้น เหมาะสำหรับโหลดสิทธิ์ทั้งหมดหลัง login แล้ว cache ไว้ใช้งาน — Admin token ดึงได้ทุก user, User token ดึงได้เฉพาะของตัวเอง"
    >
        <x-slot name="requestBody">
// Query Parameters
?system=repair-system    // required — slug ของระบบ
        </x-slot>

        <x-slot name="responses">
// 200 OK
{
  "username":    "john.doe",
  "system":      "repair-system",
  "permissions": ["view_report", "edit_order", "approve"]
}

// 403 — User token พยายาม query ผู้ใช้คนอื่น
{ "message": "Forbidden" }

// 404 — user ไม่พบหรือ inactive
{ "message": "User 'john.doe' not found" }

// 404 — system slug ไม่ถูกต้อง
{ "message": "No query results for model [App\\Models\\System]." }
        </x-slot>

        <x-slot name="example">
// PHP
$res = Http::withToken($token)
    ->get('{{ url("/api/users/john.doe/permissions") }}', [
        'system' => 'repair-system',
    ]);
$permissions = $res->json('permissions'); // ["view_report", "edit_order"]

// JavaScript
const res = await fetch('{{ url("/api/users/john.doe/permissions") }}?system=repair-system', {
    headers: { 'Authorization': 'Bearer ' + token },
});
const { permissions } = await res.json();
        </x-slot>
    </x-api-endpoint>

    {{-- GET /api/users/{username}/permissions/all --}}
    <x-api-endpoint
        method="GET"
        path="/api/users/{username}/permissions/all"
        summary="ดึง permissions ของ user ในทุกระบบพร้อมกัน"
        auth="true"
        description="คืน permissions แบบ group by system slug ในคำสั่งเดียว (ใช้ single JOIN query ไม่มี N+1) เหมาะสำหรับ portal หรือ dashboard ที่ต้องแสดงสิทธิ์ทุกระบบ — Admin token ดึงได้ทุก user, User token ดึงได้เฉพาะของตัวเอง"
    >
        <x-slot name="requestBody">
// ไม่มี query parameters — คืนทุกระบบที่ active
        </x-slot>

        <x-slot name="responses">
// 200 OK
{
  "username": "john.doe",
  "systems": {
    "repair-system": ["view_report", "edit_order", "approve"],
    "hr-system":     ["view_employee"],
    "finance":       ["view_budget"]
  }
}

// 403 — User token พยายาม query ผู้ใช้คนอื่น
{ "message": "Forbidden" }

// 404 — user ไม่พบหรือ inactive
{ "message": "User 'john.doe' not found" }
        </x-slot>

        <x-slot name="example">
// PHP
$res = Http::withToken($token)
    ->get('{{ url("/api/users/john.doe/permissions/all") }}');
$systems = $res->json('systems');
// $systems['repair-system'] => ["view_report", ...]

// JavaScript
const res = await fetch('{{ url("/api/users/john.doe/permissions/all") }}', {
    headers: { 'Authorization': 'Bearer ' + token },
});
const { systems } = await res.json();
        </x-slot>
    </x-api-endpoint>

</x-api-group>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Integration Guide --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="mt-8 bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
        <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-4.5 h-4.5 text-emerald-600" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-slate-800 text-sm">Integration Guide — ระบบใหม่ที่ใช้ UCM Auth</h3>
            <p class="text-xs text-slate-400 mt-0.5">ตัวอย่างการ integrate ระบบ Laravel ใหม่กับ UCM แบบสมบูรณ์</p>
        </div>
    </div>
    <div class="p-6 space-y-6">

        {{-- Step 1 --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">1</span>
                <h4 class="text-sm font-bold text-slate-700">ลงทะเบียนระบบใหม่ใน UCM</h4>
            </div>
            <p class="text-sm text-slate-600 ml-8">ไปที่ <strong>ระบบที่เชื่อมต่อ → สร้างระบบใหม่</strong> กำหนด slug เช่น <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono text-xs">my-new-system</code> แล้วสร้าง Permission keys ที่ต้องการ เช่น <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono text-xs">view_dashboard</code>, <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono text-xs">manage_users</code></p>
        </div>

        {{-- Step 2 --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">2</span>
                <h4 class="text-sm font-bold text-slate-700">Login Controller ของระบบใหม่</h4>
            </div>
            <div class="ml-8">
                <x-api-code-block>
// LoginController.php ของระบบใหม่
public function login(Request $request)
{
    $request->validate(['username' => 'required', 'password' => 'required']);

    $res = Http::post('{{ url("/api/auth/user-login") }}', [
        'username' => $request->username,
        'password' => $request->password,
        'system'   => 'my-new-system',
    ]);

    if ($res->status() === 401) {
        return back()->withErrors(['username' => 'username หรือ password ไม่ถูกต้อง']);
    }
    if ($res->status() === 403) {
        return back()->withErrors(['username' => 'บัญชีนี้ยังไม่ได้รับสิทธิ์ในระบบ']);
    }

    $data = $res->json();

    session([
        'ucm_token'   => $data['token'],
        'auth_user'   => $data['user'],
        'permissions' => $data['permissions'], // cache ไว้ใช้ตลอด session
    ]);

    return redirect()->intended('/dashboard');
}
                </x-api-code-block>
            </div>
        </div>

        {{-- Step 3 --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">3</span>
                <h4 class="text-sm font-bold text-slate-700">Permission Middleware ของระบบใหม่</h4>
            </div>
            <div class="ml-8">
                <x-api-code-block>
// CheckPermission.php middleware
public function handle(Request $request, Closure $next, string $permission)
{
    $permissions = session('permissions', []);

    if (! in_array($permission, $permissions)) {
        abort(403, 'ไม่มีสิทธิ์เข้าถึง');
    }

    return $next($request);
}

// ใช้ใน routes.php
Route::get('/reports', ReportController::class)
    ->middleware('permission:view_dashboard');
                </x-api-code-block>
            </div>
        </div>

        {{-- Step 4 --}}
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center flex-shrink-0">4</span>
                <h4 class="text-sm font-bold text-slate-700">Logout</h4>
            </div>
            <div class="ml-8">
                <x-api-code-block>
public function logout()
{
    $token = session('ucm_token');
    if ($token) {
        Http::withToken($token)->delete('{{ url("/api/auth/token") }}');
    }
    session()->flush();
    return redirect('/login');
}
                </x-api-code-block>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
(function () {
    // Toggle endpoint details
    document.querySelectorAll('.endpoint-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id      = this.dataset.target;
            var content = document.getElementById(id);
            var icon    = this.querySelector('.chevron');
            var isOpen  = content.style.display !== 'none';
            content.style.display = isOpen ? 'none' : 'block';
            if (icon) icon.style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    });

    // Copy to clipboard
    document.querySelectorAll('.copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var code = this.closest('.code-block-wrapper').querySelector('code').textContent;
            navigator.clipboard.writeText(code.trim()).then(function () {
                btn.textContent = 'คัดลอกแล้ว!';
                btn.classList.add('text-emerald-600');
                setTimeout(function () {
                    btn.textContent = 'คัดลอก';
                    btn.classList.remove('text-emerald-600');
                }, 1800);
            });
        });
    });

    // Tab switching inside endpoints
    document.querySelectorAll('.tab-switcher').forEach(function (switcher) {
        switcher.querySelectorAll('[data-tab-btn]').forEach(function (tabBtn) {
            tabBtn.addEventListener('click', function () {
                var group  = this.closest('.tab-switcher');
                var target = this.dataset.tabBtn;

                group.querySelectorAll('[data-tab-btn]').forEach(function (b) {
                    b.className = b.className.replace('border-indigo-500 text-indigo-600', 'border-transparent text-slate-400 hover:text-slate-600');
                });
                this.className = this.className.replace('border-transparent text-slate-400 hover:text-slate-600', 'border-indigo-500 text-indigo-600');

                group.closest('.endpoint-body').querySelectorAll('[data-tab-content]').forEach(function (panel) {
                    panel.style.display = panel.dataset.tabContent === target ? 'block' : 'none';
                });
            });
        });
    });
})();
</script>
@endpush
@endsection
