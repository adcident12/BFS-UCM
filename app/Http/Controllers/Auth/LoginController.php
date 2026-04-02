<?php

namespace App\Http\Controllers\Auth;

use App\Auth\LdapUserProvider;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        try {
            $attempted = Auth::attempt($credentials, $request->boolean('remember'));
        } catch (\Throwable $e) {
            Log::error('Login: Auth::attempt threw exception', ['error' => $e->getMessage()]);
            $attempted = false;
        }

        // ตรวจสอบว่า LDAP server ล่มหรือไม่ (ต้องตรวจก่อนแสดง error ให้ user)
        $ldapProvider = Auth::getProvider();
        $ldapDown = ($ldapProvider instanceof LdapUserProvider) && $ldapProvider->isServerUnreachable();

        if ($attempted) {
            $user = Auth::user();
            $allowedDept = config('auth.allowed_department');

            if ($allowedDept && $user->department !== $allowedDept) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                AuditLogger::log(
                    AuditLog::CATEGORY_AUTH,
                    AuditLog::EVENT_LOGIN_FAILED,
                    "เข้าสู่ระบบล้มเหลว: {$request->username} — แผนก {$user->department} ไม่มีสิทธิ์เข้าใช้งาน",
                    ['username' => $request->username, 'reason' => 'department_not_allowed', 'department' => $user->department],
                    null,
                    null, null, null,
                    $request,
                );

                app(NotificationService::class)->dispatch('login_failed', [
                    'username' => $request->username,
                    'reason' => 'department_not_allowed',
                    'department' => $user->department,
                    'description' => "เข้าสู่ระบบล้มเหลว: {$request->username} — แผนกไม่มีสิทธิ์เข้าใช้งาน",
                ]);

                return back()->withErrors([
                    'username' => 'คุณไม่มีสิทธิ์เข้าใช้งานระบบนี้',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            /** @var UcmUser $user */
            $user->update(['last_login_at' => now()]);

            AuditLogger::log(
                AuditLog::CATEGORY_AUTH,
                AuditLog::EVENT_LOGIN,
                "เข้าสู่ระบบสำเร็จ: {$user->username} ({$user->name})",
                ['department' => $user->department, 'is_admin' => $user->is_admin],
                $user,
                'user', $user->id, $user->username,
                $request,
            );

            return redirect()->route('dashboard');
        }

        // LDAP server ไม่ตอบสนอง — แสดง error ที่ถูกต้องแทนที่จะบอกว่า password ผิด
        if ($ldapDown) {
            Log::critical('Login: LDAP server unreachable — users cannot authenticate', [
                'username' => $request->username,
            ]);

            return back()->withErrors([
                'username' => 'ไม่สามารถเชื่อมต่อกับ Active Directory ได้ในขณะนี้ กรุณาติดต่อ IT Support',
            ])->onlyInput('username');
        }

        $ucmUser = UcmUser::where('username', $request->username)->first();

        AuditLogger::log(
            AuditLog::CATEGORY_AUTH,
            AuditLog::EVENT_LOGIN_FAILED,
            "เข้าสู่ระบบล้มเหลว: {$request->username} — รหัสผ่านไม่ถูกต้อง",
            ['username' => $request->username, 'reason' => 'invalid_credentials'],
            $ucmUser,
            null, null, null,
            $request,
        );

        app(NotificationService::class)->dispatch('login_failed', [
            'username' => $request->username,
            'reason' => 'invalid_credentials',
            'description' => "เข้าสู่ระบบล้มเหลว: {$request->username} — รหัสผ่านไม่ถูกต้อง",
        ]);

        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLogger::log(
                AuditLog::CATEGORY_AUTH,
                AuditLog::EVENT_LOGOUT,
                "ออกจากระบบ: {$user->username} ({$user->name})",
                [],
                $user,
                'user', $user->id, $user->username,
                $request,
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
