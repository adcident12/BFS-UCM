<?php

namespace App\Http\Controllers;

use App\Adapters\AdapterFactory;
use App\Jobs\SyncPermissionsJob;
use App\Models\AuditLog;
use App\Models\SyncLog;
use App\Models\System;
use App\Models\SystemPermission;
use App\Models\UcmUser;
use App\Models\UserSystemPermission;
use App\Services\AuditLogger;
use App\Services\LdapService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(protected LdapService $ldap) {}

    private function authUser(): ?UcmUser
    {
        /** @var UcmUser|null */
        return Auth::user();
    }

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

    public function permissionTimeline(UcmUser $user): View
    {
        $logs = AuditLog::where('subject_type', 'user')
            ->where('subject_id', $user->id)
            ->where('event_category', AuditLog::CATEGORY_PERMISSIONS)
            ->with('actor')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        $systems = System::where('is_active', true)
            ->with(['permissions' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('name')
            ->get();

        $permsBySystem = UserSystemPermission::where('user_id', $user->id)
            ->whereIn('system_id', $systems->pluck('id'))
            ->get()
            ->groupBy('system_id')
            ->map(fn ($rows) => $rows->pluck('permission_key')->toArray())
            ->toArray();

        return view('users.permission-timeline', compact('user', 'logs', 'systems', 'permsBySystem'));
    }

    public function inactive(Request $request): View
    {
        abort_unless($this->authUser()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถดูรายงานนี้ได้');

        $days = (int) $request->input('days', 30);
        abort_unless(in_array($days, [30, 60, 90], true), 422);

        $search = $request->input('search');
        $cutoff = now()->subDays($days);

        $query = UcmUser::where('is_active', true)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_login_at')
                    ->orWhere('last_login_at', '<', $cutoff);
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $users = $query
            ->withCount('systemPermissions')
            ->orderBy('last_login_at')
            ->paginate(30)
            ->withQueryString();

        return view('users.inactive', compact('users', 'days', 'search'));
    }

    public function show(UcmUser $user)
    {
        $systems = System::where('is_active', true)
            ->with([
                'permissions' => fn ($q) => $q->orderBy('sort_order'),
                'connectorConfig',
            ])
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
            $remotePerms = null;
            $accountStatus = null;

            if (AdapterFactory::hasAdapter($system)) {
                try {
                    $adapter = AdapterFactory::make($system);
                    $remotePerms = $adapter->getCurrentPermissions($user);
                    $accountStatus = $adapter->getAccountStatus($user);
                } catch (\Throwable) {
                    // remote ล้มเหลว fallback ใช้ UCM
                }
            }

            if ($remotePerms !== null) {
                $outOfSync = array_values(array_diff($ucmPerms, $remotePerms)) !== [] ||
                             array_values(array_diff($remotePerms, $ucmPerms)) !== [];
                $permsPerSystem[$system->id] = [
                    'perms' => $remotePerms,
                    'ucmPerms' => $ucmPerms,
                    'source' => 'remote',
                    'outOfSync' => $outOfSync && ! empty($ucmPerms),
                    'accountStatus' => $accountStatus,
                ];
            } else {
                $permsPerSystem[$system->id] = [
                    'perms' => $ucmPerms,
                    'ucmPerms' => $ucmPerms,
                    'source' => 'ucm',
                    'outOfSync' => false,
                    'accountStatus' => $accountStatus,
                ];
            }
        }

        return view('users.show', compact('user', 'systems', 'latestSyncLogs', 'permsPerSystem'));
    }

    public function updatePermissions(Request $request, UcmUser $user)
    {
        abort_unless($this->authUser()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถแก้ไขสิทธิ์ผู้ใช้ได้');

        $validated = $request->validate([
            'system_id' => 'required|integer|exists:systems,id',
            'permissions' => 'array',
            'permissions.*' => 'string|max:100',
            'exclusive_group' => 'array',
            'exclusive_group.*' => 'nullable|string|max:100',
        ]);

        $system = System::findOrFail($validated['system_id']);

        // รวม checkbox + radio permissions
        $checkboxPerms = $validated['permissions'] ?? [];
        $radioPerms = array_values(array_filter($validated['exclusive_group'] ?? []));
        $allSubmitted = array_merge($checkboxPerms, $radioPerms);

        // ตรวจสอบว่า permission keys ที่ส่งมาเป็นของระบบนี้จริง (ป้องกัน injection)
        $validKeys = SystemPermission::where('system_id', $system->id)
            ->pluck('key')
            ->toArray();

        $newPerms = array_values(array_intersect($allSubmitted, $validKeys));

        $adminId = Auth::id();
        $now = now();

        DB::transaction(function () use ($user, $system, $newPerms, $adminId, $now) {
            UserSystemPermission::where('user_id', $user->id)
                ->where('system_id', $system->id)
                ->delete();

            if (! empty($newPerms)) {
                $rows = array_map(fn ($key) => [
                    'user_id' => $user->id,
                    'system_id' => $system->id,
                    'permission_key' => $key,
                    'granted_by' => $adminId,
                    'granted_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $newPerms);

                UserSystemPermission::insert($rows);
            }
        });

        $syncLog = SyncLog::create([
            'user_id' => $user->id,
            'system_id' => $system->id,
            'performed_by' => $adminId,
            'action' => 'sync',
            'payload' => ['permissions' => $newPerms],
            'status' => 'pending',
        ]);

        if (AdapterFactory::hasAdapter($system)) {
            SyncPermissionsJob::dispatch($syncLog);
            $msg = "อัปเดตสิทธิ์ {$user->name} ในระบบ {$system->name} เรียบร้อย (กำลัง sync...)";
        } else {
            $syncLog->update(['status' => 'success', 'synced_at' => now()]);
            $msg = "บันทึกสิทธิ์ {$user->name} ในระบบ {$system->name} เรียบร้อย";
        }

        AuditLogger::log(
            AuditLog::CATEGORY_PERMISSIONS,
            AuditLog::EVENT_PERMISSIONS_UPDATED,
            "อัปเดตสิทธิ์ {$user->name} ({$user->username}) ในระบบ {$system->name}: ".implode(', ', $newPerms ?: ['(ไม่มีสิทธิ์)']),
            ['system_id' => $system->id, 'system_name' => $system->name, 'permissions' => $newPerms],
            Auth::user(),
            'user', $user->id, $user->username,
        );

        app(NotificationService::class)->dispatch('permissions_updated', [
            'username' => $user->username,
            'name' => $user->name,
            'system' => $system->name,
            'permissions' => implode(', ', $newPerms ?: ['(ไม่มีสิทธิ์)']),
            'performed_by' => Auth::user()?->username,
            'description' => "อัปเดตสิทธิ์ {$user->name} ในระบบ {$system->name}",
        ]);

        return back()->with('success', $msg);
    }

    public function setSystemStatus(Request $request, UcmUser $user, System $system)
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถเปิด/ปิด account ในระบบได้');

        $validated = $request->validate(['active' => 'required|boolean']);

        if (! AdapterFactory::hasAdapter($system)) {
            return back()->withErrors(['ระบบ '.$system->name.' ไม่มี adapter']);
        }

        $active = (bool) $validated['active'];
        $adapter = AdapterFactory::make($system);
        $ok = $adapter->setAccountStatus($user, $active);

        $label = $active ? 'เปิดใช้งาน' : 'ปิดการใช้งาน';

        if ($ok) {
            AuditLogger::log(
                AuditLog::CATEGORY_PERMISSIONS,
                AuditLog::EVENT_ACCOUNT_STATUS_CHANGED,
                "{$label} account {$user->name} ({$user->username}) ในระบบ {$system->name}",
                ['system_id' => $system->id, 'system_name' => $system->name, 'active' => $active],
                Auth::user(),
                'user', $user->id, $user->username,
            );

            app(NotificationService::class)->dispatch('account_status_changed', [
                'user_id'     => $user->id,
                'username'    => $user->username,
                'name'        => $user->name,
                'system_id'   => $system->id,
                'system_name' => $system->name,
                'active'      => $active,
                'description' => "{$label} account {$user->name} ({$user->username}) ในระบบ {$system->name}",
            ]);
        }

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? "{$label} account {$user->name} ใน {$system->name} เรียบร้อย"
                : "ไม่สามารถ{$label} account ได้"
        );
    }

    public function discoverFromRemote(UcmUser $user, System $system)
    {
        abort_unless($this->authUser()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถ Discover สิทธิ์ได้');

        if (! AdapterFactory::hasAdapter($system)) {
            return back()->withErrors(['ระบบ '.$system->name.' ไม่มี adapter']);
        }

        $adapter = AdapterFactory::make($system);
        $remotePerms = $adapter->getCurrentPermissions($user);

        if ($remotePerms === null) {
            return back()->withErrors(['ไม่สามารถดึงข้อมูลสิทธิ์จาก '.$system->name.' ได้']);
        }

        // กรองเฉพาะ keys ที่มีอยู่จริงในระบบ (ป้องกัน injection)
        $validKeys = SystemPermission::where('system_id', $system->id)->pluck('key')->toArray();
        $newPerms = array_values(array_intersect($remotePerms, $validKeys));

        $adminId = Auth::id();
        $now = now();

        DB::transaction(function () use ($user, $system, $newPerms, $adminId, $now) {
            UserSystemPermission::where('user_id', $user->id)
                ->where('system_id', $system->id)
                ->delete();

            if (! empty($newPerms)) {
                $rows = array_map(fn ($key) => [
                    'user_id' => $user->id,
                    'system_id' => $system->id,
                    'permission_key' => $key,
                    'granted_by' => $adminId,
                    'granted_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $newPerms);

                UserSystemPermission::insert($rows);
            }
        });

        SyncLog::create([
            'user_id' => $user->id,
            'system_id' => $system->id,
            'performed_by' => $adminId,
            'action' => 'discover',
            'payload' => ['permissions' => $newPerms],
            'status' => 'success',
            'synced_at' => $now,
        ]);

        $count = count($newPerms);

        AuditLogger::log(
            AuditLog::CATEGORY_PERMISSIONS,
            AuditLog::EVENT_PERMISSIONS_DISCOVERED,
            "Discover สิทธิ์ {$user->name} ({$user->username}) จากระบบ {$system->name}: {$count} สิทธิ์",
            ['system_id' => $system->id, 'system_name' => $system->name, 'permissions' => $newPerms, 'count' => $count],
            Auth::user(),
            'user', $user->id, $user->username,
        );

        return back()->with('success', "Discover สิทธิ์ {$user->name} จาก {$system->name} เรียบร้อย ({$count} สิทธิ์)");
    }

    public function updateInfo(Request $request, UcmUser $user)
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถแก้ไขข้อมูลผู้ใช้ได้');

        $validated = $request->validate([
            'employee_number' => 'nullable|string|max:50',
        ]);

        $old = $user->employee_number;
        $user->update(['employee_number' => $validated['employee_number'] ?: null]);

        AuditLogger::log(
            AuditLog::CATEGORY_USERS,
            AuditLog::EVENT_USER_INFO_UPDATED,
            "อัปเดตรหัสพนักงาน {$user->name} ({$user->username}): '{$old}' → '{$user->employee_number}'",
            ['old_employee_number' => $old, 'new_employee_number' => $user->employee_number],
            Auth::user(),
            'user', $user->id, $user->username,
        );

        return back()->with('success', "บันทึกรหัสพนักงาน {$user->name} เรียบร้อย");
    }

    public function importBulkFromLdap(Request $request)
    {
        abort_unless($this->authUser()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถนำเข้าผู้ใช้ได้');

        $validated = $request->validate([
            'usernames' => 'required|array|min:1',
            'usernames.*' => 'string|max:100',
            'system_id' => 'nullable|integer|exists:systems,id',
        ]);

        // อนุญาต execution นานขึ้นสำหรับ import จำนวนมาก (ไม่ block user ปกติ)
        set_time_limit(300);

        $imported = 0;
        $skipped = 0;
        $adminId = Auth::id();
        $now = now();

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
                    'name' => $ldapUser['name'],
                    'email' => $ldapUser['email'],
                    'department' => $ldapUser['department'],
                    'title' => $ldapUser['title'],
                    'ldap_dn' => $ldapUser['dn'],
                    'ldap_guid' => $ldapUser['guid'] ?: null,
                    'is_active' => true,
                ]
            );

            // ดึง permissions จากทุกระบบที่มี adapter แล้ว batch insert
            foreach ($allSystems as $sys) {
                try {
                    $adapter = AdapterFactory::make($sys);
                    $remotePerms = $adapter->getCurrentPermissions($ucmUser);

                    if (! empty($remotePerms)) {
                        DB::transaction(function () use ($ucmUser, $sys, $remotePerms, $adminId, $now) {
                            UserSystemPermission::where('user_id', $ucmUser->id)
                                ->where('system_id', $sys->id)
                                ->delete();

                            $rows = array_map(fn ($key) => [
                                'user_id' => $ucmUser->id,
                                'system_id' => $sys->id,
                                'permission_key' => $key,
                                'granted_by' => $adminId,
                                'granted_at' => $now,
                                'created_at' => $now,
                                'updated_at' => $now,
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

        AuditLogger::log(
            AuditLog::CATEGORY_USERS,
            AuditLog::EVENT_USER_BULK_IMPORTED,
            "Bulk import จาก LDAP: {$imported} คน (ข้าม {$skipped} คน)",
            ['usernames' => $validated['usernames'], 'imported' => $imported, 'skipped' => $skipped, 'systems' => $systemNames],
            Auth::user(),
        );

        app(NotificationService::class)->dispatch('user_bulk_imported', [
            'imported' => $imported,
            'skipped' => $skipped,
            'performed_by' => Auth::user()?->username,
            'description' => "Bulk import {$imported} คนจาก LDAP (ข้าม {$skipped} คน)",
        ]);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $imported,
                'skipped' => $skipped,
                'redirect' => route('users.index'),
            ]);
        }

        return redirect()->route('users.index')->with('success', $msg);
    }

    public function checkAdStatus()
    {
        abort_unless($this->authUser()?->isAdmin(), 403);

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
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่สามารถลบผู้ใช้ได้');

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1|max:200',
            'user_ids.*' => 'integer|exists:ucm_users,id',
        ]);

        $ids = $validated['user_ids'];

        // ป้องกัน admin ลบบัญชีตัวเอง
        if (in_array(Auth::id(), $ids, true)) {
            return response()->json(['success' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้'], 422);
        }

        $removedUsers = UcmUser::whereIn('id', $ids)->select('id', 'username', 'name')->get();

        DB::transaction(function () use ($ids) {
            UserSystemPermission::whereIn('user_id', $ids)->delete();
            UcmUser::whereIn('id', $ids)->update(['is_active' => false]);
            UcmUser::whereIn('id', $ids)->delete(); // soft delete
        });

        AuditLogger::log(
            AuditLog::CATEGORY_USERS,
            AuditLog::EVENT_USER_REMOVED,
            'ลบผู้ใช้ '.count($ids).' คน: '.$removedUsers->pluck('username')->join(', '),
            ['user_ids' => $ids, 'users' => $removedUsers->map(fn ($u) => ['id' => $u->id, 'username' => $u->username, 'name' => $u->name])->toArray()],
            Auth::user(),
        );

        app(NotificationService::class)->dispatch('user_removed', [
            'count' => count($ids),
            'users' => $removedUsers->pluck('username')->join(', '),
            'performed_by' => Auth::user()?->username,
            'description' => 'ลบผู้ใช้ '.count($ids).' คน: '.$removedUsers->pluck('username')->join(', '),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ลบผู้ใช้ '.count($ids).' คน และเคลียร์สิทธิ์ทั้งหมดเรียบร้อย',
        ]);
    }

    public function adminLevels()
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่จัดการสิทธิ์ admin ได้');

        $users = UcmUser::whereNull('deleted_at')
            ->select('id', 'username', 'name', 'department', 'is_admin')
            ->orderByDesc('is_admin')
            ->orderBy('name')
            ->get();

        return view('users.admin-levels', compact('users'));
    }

    public function updateAdminLevel(Request $request, UcmUser $user)
    {
        abort_unless($this->authUser()?->isSuperAdmin(), 403, 'เฉพาะ Admin ระดับ 2 เท่านั้นที่จัดการสิทธิ์ admin ได้');

        $validated = $request->validate(['level' => 'required|integer|in:0,1,2']);
        $level = (int) $validated['level'];

        // ป้องกัน super admin ลดระดับตัวเอง
        if ($user->id === Auth::id() && $level < 2) {
            return back()->withErrors(['ไม่สามารถลดระดับสิทธิ์ของตัวเองได้']);
        }

        $oldLevel = $user->is_admin;
        $user->update(['is_admin' => $level]);

        $levelName = match ($level) {
            2 => 'Admin ระดับ 2',
            1 => 'Admin ระดับ 1',
            default => 'ผู้ใช้ทั่วไป',
        };
        $oldLevelName = match ($oldLevel) {
            2 => 'Admin ระดับ 2',
            1 => 'Admin ระดับ 1',
            default => 'ผู้ใช้ทั่วไป',
        };

        AuditLogger::log(
            AuditLog::CATEGORY_USERS,
            AuditLog::EVENT_ADMIN_LEVEL_UPDATED,
            "เปลี่ยนระดับสิทธิ์ {$user->name} ({$user->username}): {$oldLevelName} → {$levelName}",
            ['old_level' => $oldLevel, 'new_level' => $level],
            Auth::user(),
            'user', $user->id, $user->username,
        );

        app(NotificationService::class)->dispatch('admin_level_updated', [
            'username' => $user->username,
            'name' => $user->name,
            'old_level' => $oldLevelName,
            'new_level' => $levelName,
            'performed_by' => Auth::user()?->username,
            'description' => "เปลี่ยนระดับสิทธิ์ {$user->name} ({$user->username}): {$oldLevelName} → {$levelName}",
        ]);

        return back()->with('success', "อัปเดตสิทธิ์ {$user->name} เป็น {$levelName} เรียบร้อย");
    }

    public function export(Request $request): StreamedResponse
    {
        $userIds = array_filter(array_map('intval', (array) $request->input('user_ids', [])));

        $query = UcmUser::where('is_active', true)->orderBy('name');
        if (! empty($userIds)) {
            $query->whereIn('id', $userIds);
        }
        $users = $query->get();

        $systems = System::where('is_active', true)->orderBy('name')->get();

        // โหลด permissions ทุก user ในคำสั่งเดียว (ไม่ N+1)
        $permRows = UserSystemPermission::whereIn('user_id', $users->pluck('id'))
            ->join('systems', function ($j) {
                $j->on('user_system_permissions.system_id', '=', 'systems.id')
                    ->where('systems.is_active', true)
                    ->whereNull('systems.deleted_at');
            })
            ->select('user_system_permissions.user_id', 'systems.name as system_name', 'user_system_permissions.permission_key')
            ->get();

        // group: user_id → system_name → "key1, key2"
        $permsMap = [];
        foreach ($permRows as $row) {
            $permsMap[$row->user_id][$row->system_name][] = $row->permission_key;
        }

        $filename = 'ucm-users-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($users, $systems, $permsMap) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header
            $header = ['username', 'employee_number', 'name', 'email', 'department', 'title'];
            foreach ($systems as $sys) {
                $header[] = $sys->name.'_permissions';
            }
            fputcsv($handle, $header);

            // Rows
            foreach ($users as $user) {
                $row = [
                    $user->username,
                    $user->employee_number ?? '',
                    $user->name,
                    $user->email ?? '',
                    $user->department ?? '',
                    $user->title ?? '',
                ];
                foreach ($systems as $sys) {
                    $keys = $permsMap[$user->id][$sys->name] ?? [];
                    $row[] = implode(', ', $keys);
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function searchLdap(Request $request)
    {
        abort_unless($this->authUser()?->isAdmin(), 403);

        $request->validate(['q' => 'required|string|min:2|max:100']);

        $users = $this->ldap->searchUsers($request->input('q'), 50);

        return response()->json($users);
    }

    public function importFromLdap(Request $request)
    {
        abort_unless($this->authUser()?->isAdmin(), 403, 'เฉพาะ Admin ระดับ 1 ขึ้นไปเท่านั้นที่สามารถนำเข้าผู้ใช้ได้');

        $validated = $request->validate(['username' => 'required|string|max:100']);

        $ldapUser = $this->ldap->findUser($validated['username']);
        if (! $ldapUser) {
            return back()->withErrors(['username' => 'ไม่พบผู้ใช้ใน LDAP']);
        }

        $user = UcmUser::updateOrCreate(
            ['username' => $ldapUser['username']],
            [
                'employee_number' => $ldapUser['employee_number'] ?: null,
                'name' => $ldapUser['name'],
                'email' => $ldapUser['email'],
                'department' => $ldapUser['department'],
                'title' => $ldapUser['title'],
                'ldap_dn' => $ldapUser['dn'],
                'ldap_guid' => $ldapUser['guid'] ?: null,
                'is_active' => true,
            ]
        );

        AuditLogger::log(
            AuditLog::CATEGORY_USERS,
            AuditLog::EVENT_USER_IMPORTED,
            "นำเข้าผู้ใช้ {$user->name} ({$user->username}) จาก LDAP",
            ['username' => $user->username, 'department' => $user->department, 'was_existing' => ! $user->wasRecentlyCreated],
            Auth::user(),
            'user', $user->id, $user->username,
        );

        app(NotificationService::class)->dispatch('user_imported', [
            'username' => $user->username,
            'name' => $user->name,
            'department' => $user->department,
            'performed_by' => Auth::user()?->username,
            'description' => "นำเข้าผู้ใช้ {$user->name} ({$user->username}) จาก LDAP",
        ]);

        return redirect()->route('users.show', $user)->with('success', "นำเข้าผู้ใช้ {$user->name} เรียบร้อย");
    }
}
