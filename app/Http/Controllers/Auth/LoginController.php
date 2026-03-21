<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\UcmUser;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
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

                return back()->withErrors([
                    'username' => 'คุณไม่มีสิทธิ์เข้าใช้งานระบบนี้',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            AuditLogger::log(
                AuditLog::CATEGORY_AUTH,
                AuditLog::EVENT_LOGIN,
                "เข้าสู่ระบบสำเร็จ: {$user->username} ({$user->name})",
                ['department' => $user->department, 'is_admin' => $user->is_admin],
                $user,
                'user', $user->id, $user->username,
                $request,
            );

            return redirect()->intended(route('dashboard'));
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

        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
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
