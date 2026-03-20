<?php

namespace App\Adapters;

use App\Models\SystemPermission;
use App\Models\UcmUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

/**
 * Adapter สำหรับ efiling system
 *
 * Permission mapping อ่านจาก system_permissions ใน UCM DB (dynamic)
 * โดยใช้ remote_value เป็นค่าที่เก็บใน efiling (fallback ใช้ label)
 *
 * Group ที่รองรับ:
 *   'Role'              → exclusive, เขียนลง users.role
 *   'Department'        → CSV, เขียนลง permission_dep
 *   'Document Category' → CSV, เขียนลง permission_cat
 *
 * User ระบุด้วย user_name ในรูปแบบ username ตรงๆ (ไม่มี bfsasia\ prefix)
 */
class EFilingAdapter extends BaseAdapter
{
    // Priority สำหรับ role (สูงสุดก่อน) — ใช้ key ของ UCM
    protected const ROLE_PRIORITY_KEYS = ['role_admin', 'role_editable', 'role_readonly'];

    protected function getDriver(): string
    {
        return 'sqlsrv';
    }

    // ----------------------------------------------------------------
    // Dynamic permission maps จาก DB
    // ----------------------------------------------------------------

    /** โหลด permissions จาก UCM DB แล้วแยกเป็น 3 map */
    protected function buildMaps(): array
    {
        $this->system->loadMissing('permissions');

        $roleMap = [];
        $depMap  = [];
        $catMap  = [];

        foreach ($this->system->permissions as $perm) {
            $remoteVal = $perm->remote_value ?: $perm->label;
            match ($perm->group) {
                'Role'              => $roleMap[$perm->key] = $remoteVal,
                'Department'        => $depMap[$perm->key]  = $remoteVal,
                'Document Category' => $catMap[$perm->key]  = $remoteVal,
                default             => null,
            };
        }

        return [$roleMap, $depMap, $catMap];
    }

    // ----------------------------------------------------------------
    // Interface methods
    // ----------------------------------------------------------------

    public function getAvailablePermissions(): array
    {
        $this->system->loadMissing('permissions');
        $perms = [];

        foreach ($this->system->permissions->sortBy('sort_order') as $p) {
            $perms[] = [
                'key'   => $p->key,
                'label' => $p->remote_value ?: $p->label,
                'group' => $p->group,
            ];
        }

        return $perms;
    }

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        $userName = $this->buildUserName($user->username);
        [$roleMap, $depMap, $catMap] = $this->buildMaps();

        try {
            $pdo = $this->getConnection();

            $stmt = $pdo->prepare("SELECT id FROM user_info WHERE user_name = ?");
            $stmt->execute([$userName]);
            $exists = $stmt->fetchColumn();

            if (! $exists) {
                Log::info("[efiling] User {$user->username} ยังไม่มี → กำลังสร้าง account...");
                if (! $this->createUser($user, $permissions)) {
                    return false;
                }
                Log::info("[efiling] สร้าง account {$user->username} สำเร็จ");
                return true;
            }

            $role    = $this->resolveRole($permissions, $roleMap);
            $depList = $this->resolveList($permissions, $depMap);
            $catList = $this->resolveList($permissions, $catMap);

            $update = $pdo->prepare(
                "UPDATE user_info SET role = ?, permission_dep = ?, permission_cat = ?, emp_no = ? WHERE user_name = ?"
            );
            $update->execute([$role, $depList, $catList, $user->employee_number ?? '', $userName]);

            Log::info("[efiling] Synced {$user->username} → role='{$role}' dep='{$depList}' cat='{$catList}'");
            return true;

        } catch (PDOException $e) {
            Log::error("[efiling] Sync failed for {$user->username}: " . $e->getMessage());
            return false;
        }
    }

    public function getCurrentPermissions(UcmUser $user): array
    {
        $userName = $this->buildUserName($user->username);
        [$roleMap, $depMap, $catMap] = $this->buildMaps();

        try {
            $pdo  = $this->getConnection();
            $stmt = $pdo->prepare("SELECT role, permission_dep, permission_cat FROM user_info WHERE user_name = ?");
            $stmt->execute([$userName]);
            $row  = $stmt->fetch();

            if (! $row) {
                return [];
            }

            $perms = [];

            $roleKey = array_search($row['role'], $roleMap, true);
            if ($roleKey) {
                $perms[] = $roleKey;
            }

            foreach (array_filter(array_map('trim', explode(',', $row['permission_dep'] ?? ''))) as $dep) {
                $key = array_search($dep, $depMap, true);
                if ($key) $perms[] = $key;
            }

            foreach (array_filter(array_map('trim', explode(',', $row['permission_cat'] ?? ''))) as $cat) {
                $key = array_search($cat, $catMap, true);
                if ($key) $perms[] = $key;
            }

            return $perms;

        } catch (PDOException $e) {
            Log::error("[efiling] getCurrentPermissions failed: " . $e->getMessage());
            return [];
        }
    }

    public function getSystemUsers(): array
    {
        try {
            $rows = $this->getConnection()
                ->query("SELECT user_name, emp_no, role, status FROM user_info WHERE user_name IS NOT NULL AND user_name <> '' ORDER BY user_name")
                ->fetchAll();

            $ucmUsernames = DB::table('ucm_users')->whereNull('deleted_at')
                ->pluck('username')->map('strtolower')->flip()->all();

            $seen = [];
            $result = [];
            foreach ($rows as $row) {
                $username = $this->buildUserName($row['user_name'] ?? '');
                if ($username === '') continue;
                $lower = strtolower($username);
                if (isset($seen[$lower])) continue; // skip duplicates
                $seen[$lower] = true;
                $result[] = [
                    'username'   => $username,
                    'name'       => $username,
                    'email'      => '',
                    'department' => '',
                    'status'     => (bool) ($row['status'] ?? 1),
                    'in_ucm'     => isset($ucmUsernames[$lower]),
                ];
            }
            return $result;

        } catch (\PDOException $e) {
            Log::error("[efiling] getSystemUsers failed: " . $e->getMessage());
            return [];
        }
    }

    public function getAccountStatus(UcmUser $user): ?bool
    {
        $userName = $this->buildUserName($user->username);
        try {
            $stmt = $this->getConnection()->prepare("SELECT status FROM user_info WHERE user_name = ?");
            $stmt->execute([$userName]);
            $row = $stmt->fetch();
            return $row ? (bool) $row['status'] : null;
        } catch (PDOException $e) {
            Log::error("[efiling] getAccountStatus failed: " . $e->getMessage());
            return null;
        }
    }

    public function setAccountStatus(UcmUser $user, bool $active): bool
    {
        $userName = $this->buildUserName($user->username);
        try {
            $stmt = $this->getConnection()->prepare("UPDATE user_info SET status = ? WHERE user_name = ?");
            $stmt->execute([$active ? 1 : 0, $userName]);
            Log::info("[efiling] setAccountStatus {$user->username} → " . ($active ? 'active' : 'disabled'));
            return true;
        } catch (PDOException $e) {
            Log::error("[efiling] setAccountStatus failed: " . $e->getMessage());
            return false;
        }
    }

    public function revokeAll(UcmUser $user): bool
    {
        [$roleMap] = $this->buildMaps();
        // หา key ที่ map ไปยัง 'read only' หรือ role ต่ำสุด
        $readonlyKey = array_search('read only', $roleMap, true)
            ?: array_key_last($roleMap);

        return $this->syncPermissions($user, $readonlyKey ? [$readonlyKey] : []);
    }

    public function createUser(UcmUser $user, array $permissions): bool
    {
        $userName = $this->buildUserName($user->username);
        [$roleMap, $depMap, $catMap] = $this->buildMaps();

        $role    = $this->resolveRole($permissions, $roleMap);
        $depList = $this->resolveList($permissions, $depMap);
        $catList = $this->resolveList($permissions, $catMap);
        $now     = now()->format('Y-m-d H:i:s.000');

        try {
            $pdo = $this->getConnection();

            $check = $pdo->prepare("SELECT id FROM user_info WHERE user_name = ?");
            $check->execute([$userName]);
            if ($check->fetchColumn()) {
                return true;
            }

            $stmt = $pdo->prepare("
                INSERT INTO user_info
                    (user_name, emp_no, role, permission_dep, permission_cat,
                     status, created_date, created_by, last_updated_date, last_updated_by)
                VALUES (?, ?, ?, ?, ?, 1, ?, 'UCM', ?, 'UCM')
            ");

            $stmt->execute([
                $userName,
                $user->employee_number ?? '',
                $role,
                $depList,
                $catList,
                $now,
                $now,
            ]);

            Log::info("[efiling] Created account {$userName} role='{$role}' dep='{$depList}'");
            return true;

        } catch (PDOException $e) {
            Log::error("[efiling] createUser failed for {$user->username}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Scan efiling DB → ค้นหา dep/cat values ที่ยังไม่มีใน UCM → สร้างอัตโนมัติ
     * คืนค่า array ของ permission key ที่สร้างใหม่
     */
    public function discoverPermissions(): array
    {
        $created = [];

        try {
            $pdo = $this->getConnection();

            // โหลด remote_values ที่มีอยู่แล้วใน UCM (แยกตาม group)
            $this->system->loadMissing('permissions');
            $existingDeps = $this->system->permissions
                ->where('group', 'Department')
                ->map(fn($p) => $p->remote_value ?: $p->label)
                ->map('strtoupper')
                ->all();
            $existingCats = $this->system->permissions
                ->where('group', 'Document Category')
                ->map(fn($p) => $p->remote_value ?: $p->label)
                ->all();

            // ---- Department ----
            $rows = $pdo->query("SELECT DISTINCT permission_dep FROM user_info WHERE permission_dep IS NOT NULL AND permission_dep != ''")->fetchAll();
            $allDepValues = collect($rows)
                ->flatMap(fn($r) => array_filter(array_map('trim', explode(',', $r['permission_dep']))))
                ->unique()->values();

            foreach ($allDepValues as $val) {
                if (in_array(strtoupper($val), array_map('strtoupper', $existingDeps), true)) continue;

                $key = 'dep_' . preg_replace('/[^a-z0-9]/', '_', strtolower($val));
                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $key],
                    ['label' => $val, 'remote_value' => $val, 'group' => 'Department', 'is_exclusive' => false, 'sort_order' => 50]
                );
                $created[] = $key;
                Log::info("[efiling] Discovered dept permission: {$key} = '{$val}'");
            }

            // ---- Document Category ----
            $rows = $pdo->query("SELECT DISTINCT permission_cat FROM user_info WHERE permission_cat IS NOT NULL AND permission_cat != ''")->fetchAll();
            $allCatValues = collect($rows)
                ->flatMap(fn($r) => array_filter(array_map('trim', explode(',', $r['permission_cat']))))
                ->unique()->values();

            foreach ($allCatValues as $val) {
                if (in_array($val, $existingCats, true)) continue;

                $key = 'cat_' . preg_replace('/[^a-z0-9]/', '_', strtolower($val));
                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $key],
                    ['label' => $val, 'remote_value' => $val, 'group' => 'Document Category', 'is_exclusive' => false, 'sort_order' => 100]
                );
                $created[] = $key;
                Log::info("[efiling] Discovered cat permission: {$key} = '{$val}'");
            }

        } catch (PDOException $e) {
            Log::error("[efiling] discoverPermissions failed: " . $e->getMessage());
        }

        return $created;
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    protected function buildUserName(string $username): string
    {
        $lower = strtolower($username);
        if (str_starts_with($lower, 'bfsasia\\')) {
            return substr($username, 8);
        }
        return $username;
    }

    protected function resolveRole(array $permissions, array $roleMap): string
    {
        // ลำดับ priority: ใช้ ROLE_PRIORITY_KEYS ก่อน ถ้าไม่มีใช้ลำดับใน map
        $priority = array_merge(
            array_intersect(self::ROLE_PRIORITY_KEYS, array_keys($roleMap)),
            array_diff(array_keys($roleMap), self::ROLE_PRIORITY_KEYS)
        );

        foreach ($priority as $key) {
            if (in_array($key, $permissions)) {
                return $roleMap[$key];
            }
        }

        // default: ค่าสุดท้ายใน roleMap (ต่ำสุด) หรือ 'read only'
        return end($roleMap) ?: 'read only';
    }

    protected function resolveList(array $permissions, array $map): string
    {
        $values = [];
        foreach ($map as $key => $dbValue) {
            if (in_array($key, $permissions)) {
                $values[] = $dbValue;
            }
        }
        return implode(',', $values);
    }
}
