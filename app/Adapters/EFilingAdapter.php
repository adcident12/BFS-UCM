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
     * สร้าง record ใน departments หรือ document_categories ของ EFiling
     * เมื่อ Admin เพิ่ม permission ใหม่จาก UCM (UCM → External)
     *
     * คืน label เป็น remote_value เพราะ user_info เก็บ name ไม่ใช่ id
     */
    public function provisionPermission(string $key, string $label, string $group): string|int|null
    {
        try {
            $pdo = $this->getConnection();

            if ($group === 'Department') {
                $check = $pdo->prepare("SELECT id FROM departments WHERE name = ?");
                $check->execute([$label]);
                if (! $check->fetchColumn()) {
                    $pdo->prepare("INSERT INTO departments (name) VALUES (?)")->execute([$label]);
                    Log::info("[efiling] provisionPermission: เพิ่ม department '{$label}'");
                }
                return $label; // remote_value = name ที่ใช้ใน permission_dep CSV
            }

            if ($group === 'Document Category') {
                $check = $pdo->prepare("SELECT id FROM document_categories WHERE name = ?");
                $check->execute([$label]);
                if (! $check->fetchColumn()) {
                    $pdo->prepare("INSERT INTO document_categories (name) VALUES (?)")->execute([$label]);
                    Log::info("[efiling] provisionPermission: เพิ่ม document_category '{$label}'");
                }
                return $label; // remote_value = name ที่ใช้ใน permission_cat CSV
            }

        } catch (PDOException $e) {
            Log::error("[efiling] provisionPermission failed for '{$label}': " . $e->getMessage());
        }

        return null;
    }

    /**
     * ลบ record ออกจาก departments หรือ document_categories
     * เมื่อ Admin ลบ permission จาก UCM (UCM → External)
     *
     * ตรวจ group ของ permission ก่อนลบ เพื่อไม่ให้ลบข้ามตาราง
     * (กรณีชื่อ Department กับ Document Category ซ้ำกัน)
     */
    public function deletePermission(string $remoteValue): bool
    {
        // ดึง group ของ permission นี้จาก UCM (record ยังไม่ถูกลบ ณ จุดนี้)
        $permission = $this->system->permissions()
            ->where('remote_value', $remoteValue)
            ->select('group')
            ->first();

        $group = $permission?->group;

        try {
            $pdo = $this->getConnection();

            if ($group === 'Department') {
                $pdo->prepare("DELETE FROM departments WHERE name = ?")->execute([$remoteValue]);
                Log::info("[efiling] deletePermission: ลบ department '{$remoteValue}'");
            } elseif ($group === 'Document Category') {
                $pdo->prepare("DELETE FROM document_categories WHERE name = ?")->execute([$remoteValue]);
                Log::info("[efiling] deletePermission: ลบ document_category '{$remoteValue}'");
            }
            // Role และ group อื่นๆ ไม่มีตาราง definition — ไม่ต้องลบอะไร

            return true;
        } catch (PDOException $e) {
            Log::error("[efiling] deletePermission failed for '{$remoteValue}': " . $e->getMessage());
            return false;
        }
    }

    /** EFiling ทั้ง provision (เพิ่ม) และ delete (ลบ) permission definitions จริง — 2-way sync */
    public function supports2WayPermissions(): bool
    {
        return true;
    }

    /** EFiling ลบ department/document_category แบบ hard delete เมื่อ admin ลบ permission ใน UCM */
    public function getPermissionDeleteMode(): \App\Enums\PermissionDeleteMode
    {
        return \App\Enums\PermissionDeleteMode::Hard;
    }

    /**
     * Scan efiling DB → ค้นหา dep/cat ที่ยังไม่มีใน UCM → สร้างอัตโนมัติ
     * scan จาก definition tables (departments, document_categories) เป็นหลัก
     * fallback scan user_info สำหรับค่า legacy ที่ไม่มีใน definition table
     */
    public function discoverPermissions(): array
    {
        $created = [];

        try {
            $pdo = $this->getConnection();

            $this->system->loadMissing('permissions');
            $existingDeps = $this->system->permissions
                ->where('group', 'Department')
                ->map(fn($p) => strtoupper($p->remote_value ?: $p->label))
                ->all();
            $existingCats = $this->system->permissions
                ->where('group', 'Document Category')
                ->map(fn($p) => strtolower($p->remote_value ?: $p->label))
                ->all();

            // ---- Department (scan จาก departments table) ----
            $depRows = $pdo->query("SELECT name FROM departments ORDER BY name")->fetchAll();
            $depNames = collect($depRows)->pluck('name')->filter()->unique()->values();

            // fallback: ดึงค่า legacy จาก user_info ที่อาจไม่มีใน departments table
            $legacyDepRows = $pdo->query("SELECT DISTINCT permission_dep FROM user_info WHERE permission_dep IS NOT NULL AND permission_dep != ''")->fetchAll();
            $legacyDeps = collect($legacyDepRows)
                ->flatMap(fn($r) => array_filter(array_map('trim', explode(',', $r['permission_dep']))))
                ->unique();

            foreach ($depNames->merge($legacyDeps)->unique() as $name) {
                if (in_array(strtoupper($name), $existingDeps, true)) continue;

                $key = 'dep_' . preg_replace('/[^a-z0-9]/', '_', strtolower($name));
                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $key],
                    ['label' => $name, 'remote_value' => $name, 'group' => 'Department', 'is_exclusive' => false, 'sort_order' => 50]
                );
                $created[] = $key;
                Log::info("[efiling] Discovered dept: {$key} = '{$name}'");
            }

            // ---- Document Category (scan จาก document_categories table) ----
            $catRows = $pdo->query("SELECT name FROM document_categories ORDER BY name")->fetchAll();
            $catNames = collect($catRows)->pluck('name')->filter()->unique()->values();

            // fallback: ดึงค่า legacy จาก user_info
            $legacyCatRows = $pdo->query("SELECT DISTINCT permission_cat FROM user_info WHERE permission_cat IS NOT NULL AND permission_cat != ''")->fetchAll();
            $legacyCats = collect($legacyCatRows)
                ->flatMap(fn($r) => array_filter(array_map('trim', explode(',', $r['permission_cat']))))
                ->unique();

            foreach ($catNames->merge($legacyCats)->unique() as $name) {
                if (in_array(strtolower($name), $existingCats, true)) continue;

                $key = 'cat_' . preg_replace('/[^a-z0-9]/', '_', strtolower($name));
                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $key],
                    ['label' => $name, 'remote_value' => $name, 'group' => 'Document Category', 'is_exclusive' => false, 'sort_order' => 100]
                );
                $created[] = $key;
                Log::info("[efiling] Discovered cat: {$key} = '{$name}'");
            }

        } catch (PDOException $e) {
            Log::error("[efiling] discoverPermissions failed: " . $e->getMessage());
        }

        return $created;
    }

    // ----------------------------------------------------------------
    // Managed Group CRUD (departments & document_categories)
    // ----------------------------------------------------------------

    public function getManagedGroups(): array
    {
        return ['Department', 'Document Category'];
    }

    public function getGroupRecords(string $group): array
    {
        $table = $this->groupTable($group);
        if (! $table) return [];

        try {
            return $this->getConnection()
                ->query("SELECT id, name FROM {$table} ORDER BY name")
                ->fetchAll();
        } catch (PDOException $e) {
            Log::error("[efiling] getGroupRecords({$group}) failed: " . $e->getMessage());
            return [];
        }
    }

    public function addGroupRecord(string $group, string $name, array $extra = []): array|false
    {
        $table = $this->groupTable($group);
        if (! $table) return false;

        try {
            $pdo = $this->getConnection();

            $check = $pdo->prepare("SELECT id, name FROM {$table} WHERE name = ?");
            $check->execute([$name]);
            if ($row = $check->fetch()) {
                return $row; // already exists — return existing
            }

            $pdo->prepare("INSERT INTO {$table} (name) VALUES (?)")->execute([$name]);
            $id = (int) $pdo->lastInsertId();
            Log::info("[efiling] addGroupRecord({$group}): เพิ่ม '{$name}' id={$id}");
            return ['id' => $id, 'name' => $name];

        } catch (PDOException $e) {
            Log::error("[efiling] addGroupRecord({$group}, '{$name}') failed: " . $e->getMessage());
            return false;
        }
    }

    public function updateGroupRecord(string $group, int $id, string $name, array $extra = []): bool
    {
        $table = $this->groupTable($group);
        if (! $table) return false;

        try {
            $pdo = $this->getConnection();
            $pdo->prepare("UPDATE {$table} SET name = ? WHERE id = ?")->execute([$name, $id]);
            Log::info("[efiling] updateGroupRecord({$group}): id={$id} → '{$name}'");
            return true;
        } catch (PDOException $e) {
            Log::error("[efiling] updateGroupRecord({$group}, {$id}) failed: " . $e->getMessage());
            return false;
        }
    }

    public function deleteGroupRecord(string $group, int $id): bool
    {
        $table = $this->groupTable($group);
        if (! $table) return false;

        try {
            $pdo = $this->getConnection();
            $pdo->prepare("DELETE FROM {$table} WHERE id = ?")->execute([$id]);
            Log::info("[efiling] deleteGroupRecord({$group}): id={$id}");
            return true;
        } catch (PDOException $e) {
            Log::error("[efiling] deleteGroupRecord({$group}, {$id}) failed: " . $e->getMessage());
            return false;
        }
    }

    private function groupTable(string $group): ?string
    {
        return match ($group) {
            'Department'        => 'departments',
            'Document Category' => 'document_categories',
            default             => null,
        };
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
