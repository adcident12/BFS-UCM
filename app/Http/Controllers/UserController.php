<?php

namespace App\Http\Controllers;

use App\Adapters\AdapterFactory;
use App\Jobs\SyncPermissionsJob;
use App\Models\System;
use App\Models\SyncLog;
use App\Models\SystemPermission;
use App\Models\UcmUser;
use App\Models\UserSystemPermission;
use App\Services\LdapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(protected LdapService $ldap) {}

    public function index(Request $request)
    {
        $query = UcmUser::query()->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $users = $query
            ->withCount('systemPermissions')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $importableSystems = System::where('is_active', true)
            ->get()
            ->filter(fn ($s) => AdapterFactory::hasAdapter($s))
            ->values();

        return view('users.index', compact('users', 'importableSystems'));
    }

    public function show(UcmUser $user)
    {
        $systems = System::where('is_active', true)
            ->with(['permissions' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        // โหลด sync logs ทุกระบบของ user ในคำสั่งเดียว (ไม่ N+1)
        $latestSyncLogs = SyncLog::where('user_id', $user->id)
            ->whereIn('system_id', $systems->pluck('id'))
            ->latest()
            ->get()
            ->unique('system_id')
            ->keyBy('system_id');

        // โหลด permissions ทั้งหมดของ user ในคำสั่งเดียว แล้ว group ใน PHP (ไม่ N+1)
        $ucmPermsBySystem = UserSystemPermission::where('user_id', $user->id)
            ->whereIn('system_id', $systems->pluck('id'))
            ->get()
            ->groupBy('system_id')
            ->map(fn ($rows) => $rows->pluck('permission_key')->toArray())
            ->toArray();

        $permsPerSystem = [];

        foreach ($systems as $system) {
            $ucmPerms = $ucmPermsBySystem[$system->id] ?? [];
            $remotePerms   = null;
            $accountStatus = null;

            if (AdapterFactory::hasAdapter($system)) {
                try {
                    $adapter       = AdapterFactory::make($system);
                    $remotePerms   = $adapter->getCurrentPermissions($user);
                    $accountStatus = $adapter->getAccountStatus($user);
                } catch (\Throwable) {
                    // remote ล้มเหลว fallback ใช้ UCM
                }
            }

            if ($remotePerms !== null) {
                $outOfSync = array_values(array_diff($ucmPerms, $remotePerms)) !== [] ||
                             array_values(array_diff($remotePerms, $ucmPerms)) !== [];
                $permsPerSystem[$system->id] = [
                    'perms'         => $remotePerms,
                    'ucmPerms'      => $ucmPerms,
                    'source'        => 'remote',
                    'outOfSync'     => $outOfSync && ! empty($ucmPerms),
                    'accountStatus' => $accountStatus,
                ];
            } else {
                $permsPerSystem[$system->id] = [
                    'perms'         => $ucmPerms,
                    'ucmPerms'      => $ucmPerms,
                    'source'        => 'ucm',
                    'outOfSync'     => false,
                    'accountStatus' => $accountStatus,
                ];
            }
        }

        return view('users.show', compact('user', 'systems', 'latestSyncLogs', 'permsPerSystem'));
    }

    public function updatePermissions(Request $request, UcmUser $user)
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถแก้ไขสิทธิ์ผู้ใช้ได้');

        $validated = $request->validate([
            'system_id'              => 'required|integer|exists:systems,id',
            'permissions'            => 'array',
            'permissions.*'          => 'string|max:100',
            'exclusive_group'        => 'array',
            'exclusive_group.*'      => 'nullable|string|max:100',
        ]);

        $system = System::findOrFail($validated['system_id']);

        // รวม checkbox + radio permissions
        $checkboxPerms = $validated['permissions'] ?? [];
        $radioPerms    = array_values(array_filter($validated['exclusive_group'] ?? []));
        $allSubmitted  = array_merge($checkboxPerms, $radioPerms);

        // ตรวจสอบว่า permission keys ที่ส่งมาเป็นของระบบนี้จริง (ป้องกัน injection)
        $validKeys = SystemPermission::where('system_id', $system->id)
            ->pluck('key')
            ->toArray();

        $newPerms = array_values(array_intersect($allSubmitted, $validKeys));

        $adminId  = Auth::id();
        $now      = now();

        DB::transaction(function () use ($user, $system, $newPerms, $adminId, $now) {
            UserSystemPermission::where('user_id', $user->id)
                ->where('system_id', $system->id)
                ->delete();

            if (! empty($newPerms)) {
                $rows = array_map(fn ($key) => [
                    'user_id'        => $user->id,
                    'system_id'      => $system->id,
                    'permission_key' => $key,
                    'granted_by'     => $adminId,
                    'granted_at'     => $now,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ], $newPerms);

                UserSystemPermission::insert($rows);
            }
        });

        $syncLog = SyncLog::create([
            'user_id'      => $user->id,
            'system_id'    => $system->id,
            'performed_by' => $adminId,
            'action'       => 'sync',
            'payload'      => ['permissions' => $newPerms],
            'status'       => 'pending',
        ]);

        if (AdapterFactory::hasAdapter($system)) {
            SyncPermissionsJob::dispatch($syncLog);
            $msg = "อัปเดตสิทธิ์ {$user->name} ในระบบ {$system->name} เรียบร้อย (กำลัง sync...)";
        } else {
            $syncLog->update(['status' => 'success', 'synced_at' => now()]);
            $msg = "บันทึกสิทธิ์ {$user->name} ในระบบ {$system->name} เรียบร้อย";
        }

        return back()->with('success', $msg);
    }

    public function setSystemStatus(Request $request, UcmUser $user, System $system)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเปิด/ปิด account ในระบบได้');

        $validated = $request->validate(['active' => 'required|boolean']);

        if (! AdapterFactory::hasAdapter($system)) {
            return back()->withErrors(['ระบบ ' . $system->name . ' ไม่มี adapter']);
        }

        $active  = (bool) $validated['active'];
        $adapter = AdapterFactory::make($system);
        $ok      = $adapter->setAccountStatus($user, $active);

        $label = $active ? 'เปิดใช้งาน' : 'ปิดการใช้งาน';

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? "{$label} account {$user->name} ใน {$system->name} เรียบร้อย"
                : "ไม่สามารถ{$label} account ได้"
        );
    }

    public function updateInfo(Request $request, UcmUser $user)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไขข้อมูลผู้ใช้ได้');

        $validated = $request->validate([
            'employee_number' => 'nullable|string|max:50',
        ]);

        $user->update(['employee_number' => $validated['employee_number'] ?: null]);

        return back()->with('success', "บันทึกรหัสพนักงาน {$user->name} เรียบร้อย");
    }

    public function importBulkFromLdap(Request $request)
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถนำเข้าผู้ใช้ได้');

        $validated = $request->validate([
            'usernames'   => 'required|array|min:1',
            'usernames.*' => 'string|max:100',
            'system_id'   => 'nullable|integer|exists:systems,id',
        ]);

        // อนุญาต execution นานขึ้นสำหรับ import จำนวนมาก (ไม่ block user ปกติ)
        set_time_limit(300);

        $imported = 0;
        $skipped  = 0;
        $adminId  = Auth::id();
        $now      = now();

        $allSystems = System::where('is_active', true)
            ->get()
            ->filter(fn ($s) => AdapterFactory::hasAdapter($s))
            ->values();

        foreach ($validated['usernames'] as $username) {
            $ldapUser = $this->ldap->findUser($username);
            if (! $ldapUser) {
                $skipped++;
                continue;
            }

            $ucmUser = UcmUser::updateOrCreate(
                ['username' => $ldapUser['username']],
                [
                    'employee_number' => $ldapUser['employee_number'] ?: null,
                    'name'            => $ldapUser['name'],
                    'email'           => $ldapUser['email'],
                    'department'      => $ldapUser['department'],
                    'title'           => $ldapUser['title'],
                    'ldap_dn'         => $ldapUser['dn'],
                    'ldap_guid'       => $ldapUser['guid'] ?: null,
                    'is_active'       => true,
                ]
            );

            // ดึง permissions จากทุกระบบที่มี adapter แล้ว batch insert
            foreach ($allSystems as $sys) {
                try {
                    $adapter     = AdapterFactory::make($sys);
                    $remotePerms = $adapter->getCurrentPermissions($ucmUser);

                    if (! empty($remotePerms)) {
                        DB::transaction(function () use ($ucmUser, $sys, $remotePerms, $adminId, $now) {
                            UserSystemPermission::where('user_id', $ucmUser->id)
                                ->where('system_id', $sys->id)
                                ->delete();

                            $rows = array_map(fn ($key) => [
                                'user_id'        => $ucmUser->id,
                                'system_id'      => $sys->id,
                                'permission_key' => $key,
                                'granted_by'     => $adminId,
                                'granted_at'     => $now,
                                'created_at'     => $now,
                                'updated_at'     => $now,
                            ], $remotePerms);

                            UserSystemPermission::insert($rows);
                        });
                    }
                } catch (\Throwable) {
                    // ถ้าระบบใดเชื่อมต่อไม่ได้ ข้ามไปไม่หยุด import
                }
            }

            $imported++;
        }

        $systemNames = $allSystems->pluck('name')->join(', ');
        $msg = "นำเข้า {$imported} คนเรียบร้อย พร้อม permissions จากทุกระบบ ({$systemNames})";
        if ($skipped) {
            $msg .= " (ไม่พบใน AD: {$skipped} คน)";
        }

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success'  => true,
                'message'  => $msg,
                'imported' => $imported,
                'skipped'  => $skipped,
                'redirect' => route('users.index'),
            ]);
        }

        return redirect()->route('users.index')->with('success', $msg);
    }

    public function checkAdStatus()
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $users = UcmUser::where('is_active', true)
            ->select('id', 'username', 'name', 'department', 'email')
            ->orderBy('name')
            ->get();

        if ($users->isEmpty()) {
            return response()->json([]);
        }

        $existsInAd = $this->ldap->findUsersExistence($users->pluck('username')->toArray());

        $missing = $users->filter(
            fn ($u) => ! in_array(strtolower($u->username), $existsInAd, true)
        )->values();

        return response()->json($missing);
    }

    public function removeUsers(Request $request)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถลบผู้ใช้ได้');

        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1|max:200',
            'user_ids.*' => 'integer|exists:ucm_users,id',
        ]);

        $ids = $validated['user_ids'];

        // ป้องกัน admin ลบบัญชีตัวเอง
        if (in_array(Auth::id(), $ids, true)) {
            return response()->json(['success' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้'], 422);
        }

        DB::transaction(function () use ($ids) {
            UserSystemPermission::whereIn('user_id', $ids)->delete();
            UcmUser::whereIn('id', $ids)->update(['is_active' => false]);
            UcmUser::whereIn('id', $ids)->delete(); // soft delete
        });

        return response()->json([
            'success' => true,
            'message' => 'ลบผู้ใช้ ' . count($ids) . ' คน และเคลียร์สิทธิ์ทั้งหมดเรียบร้อย',
        ]);
    }

    public function adminLevels()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่จัดการสิทธิ์ admin ได้');

        $users = UcmUser::whereNull('deleted_at')
            ->select('id', 'username', 'name', 'department', 'is_admin')
            ->orderByDesc('is_admin')
            ->orderBy('name')
            ->get();

        return view('users.admin-levels', compact('users'));
    }

    public function updateAdminLevel(Request $request, UcmUser $user)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่จัดการสิทธิ์ admin ได้');

        $validated = $request->validate(['level' => 'required|integer|in:0,1,2']);
        $level = (int) $validated['level'];

        // ป้องกัน super admin ลดระดับตัวเอง
        if ($user->id === Auth::id() && $level < 2) {
            return back()->withErrors(['ไม่สามารถลดระดับสิทธิ์ของตัวเองได้']);
        }

        $user->update(['is_admin' => $level]);

        $levelName = match($level) {
            2 => 'Admin ระดับ 2',
            1 => 'Admin ระดับ 1',
            default => 'ผู้ใช้ทั่วไป',
        };

        return back()->with('success', "อัปเดตสิทธิ์ {$user->name} เป็น {$levelName} เรียบร้อย");
    }

    public function searchLdap(Request $request)
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $request->validate(['q' => 'required|string|min:2|max:100']);

        $users = $this->ldap->searchUsers($request->input('q'), 50);

        return response()->json($users);
    }

    public function importFromLdap(Request $request)
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถนำเข้าผู้ใช้ได้');

        $validated = $request->validate(['username' => 'required|string|max:100']);

        $ldapUser = $this->ldap->findUser($validated['username']);
        if (! $ldapUser) {
            return back()->withErrors(['username' => 'ไม่พบผู้ใช้ใน LDAP']);
        }

        $user = UcmUser::updateOrCreate(
            ['username' => $ldapUser['username']],
            [
                'employee_number' => $ldapUser['employee_number'] ?: null,
                'name'            => $ldapUser['name'],
                'email'           => $ldapUser['email'],
                'department'      => $ldapUser['department'],
                'title'           => $ldapUser['title'],
                'ldap_dn'         => $ldapUser['dn'],
                'ldap_guid'       => $ldapUser['guid'] ?: null,
                'is_active'       => true,
            ]
        );

        return redirect()->route('users.show', $user)->with('success', "นำเข้าผู้ใช้ {$user->name} เรียบร้อย");
    }
}
