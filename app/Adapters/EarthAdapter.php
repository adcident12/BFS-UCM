<?php

namespace App\Adapters;

use App\Models\SystemPermission;
use App\Models\UcmUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

/**
 * Adapter สำหรับ EARTH — Flight Operations System (Legacy MSSQL)
 *
 * Permission model ของ earth:
 *   UserMgnt_PageGroup  pg_id, group_name, priority
 *   UserMgnt_UserGrant  user_id → pg_id + s_id
 *   UserMgnt_Status     s_id: 1=Read Only, 2=Editable, 3=Denied
 *   UserMgnt_VerifyUser user_id, username, employee_no, department, position, status(0/1)
 *
 * remote_value format (ใน UCM system_permissions): "{group_name}:{s_id}"
 *   เช่น "Daily Flight:2" = Daily Flight + Editable
 *        "PAX:1"          = PAX + Read Only
 *
 * Mapping logic:
 *   - โหลด pg_id จาก earth DB ตาม group_name (dynamic — ไม่ hardcode)
 *   - สำหรับ user แต่ละคน UPSERT UserMgnt_UserGrant ทุก menu group
 *   - menu ที่ไม่มี UCM permission → set s_id=3 (Denied)
 */
class EarthAdapter extends BaseAdapter
{
    /** cache pg_id map [group_name => pg_id] */
    private ?array $pgIdMap = null;

    protected function getDriver(): string
    {
        return 'sqlsrv';
    }

    // ── Page Group Cache ─────────────────────────────────────────────

    /**
     * โหลด map ของ group_name → pg_id จาก earth DB
     * Cache ไว้ใน instance เพื่อไม่ต้อง query ซ้ำหลายครั้ง
     */
    private function getPgIdMap(): array
    {
        if ($this->pgIdMap !== null) {
            return $this->pgIdMap;
        }

        try {
            $rows = $this->getConnection()
                ->query("SELECT pg_id, group_name FROM UserMgnt_PageGroup ORDER BY priority, pg_id")
                ->fetchAll();

            $this->pgIdMap = [];
            foreach ($rows as $row) {
                $this->pgIdMap[trim($row['group_name'])] = (int) $row['pg_id'];
            }
        } catch (PDOException $e) {
            Log::error("[earth] getPgIdMap failed: " . $e->getMessage());
            $this->pgIdMap = [];
        }

        return $this->pgIdMap;
    }

    // ── Permission Map Builders ──────────────────────────────────────

    /**
     * สร้าง map จาก UCM permissions:
     * [group_name => [s_id => ucm_key]]
     * เช่น ['Daily Flight' => [1 => 'daily_read', 2 => 'daily_edit']]
     */
    private function buildPermMap(): array
    {
        $this->system->loadMissing('permissions');
        $map = [];

        foreach ($this->system->permissions as $perm) {
            if (! $perm->remote_value) {
                continue;
            }
            $parts = explode(':', $perm->remote_value, 2);
            if (count($parts) !== 2) {
                continue;
            }
            [$groupName, $sId] = $parts;
            $map[trim($groupName)][(int) $sId] = $perm->key;
        }

        return $map;
    }

    /**
     * สำหรับรายการ UCM permission keys ที่ granted → คืน [group_name => s_id]
     * เช่น ['daily_edit', 'pax_read'] → ['Daily Flight' => 2, 'PAX' => 1]
     * group_name ที่ไม่มีใน granted → s_id=3 (Denied)
     */
    private function resolveGrantMap(array $grantedKeys, array $permMap): array
    {
        // กลับ map: ucm_key → [group_name, s_id]
        $keyToGroup = [];
        foreach ($permMap as $groupName => $sIdMap) {
            foreach ($sIdMap as $sId => $key) {
                $keyToGroup[$key] = ['group' => $groupName, 's_id' => $sId];
            }
        }

        // รวมผลลัพธ์ต่อ group (ถ้ามีหลาย key → เลือก s_id ที่ต่ำกว่า = สิทธิ์สูงกว่า)
        $result = [];
        foreach ($grantedKeys as $key) {
            if (! isset($keyToGroup[$key])) {
                continue;
            }
            $groupName = $keyToGroup[$key]['group'];
            $sId       = $keyToGroup[$key]['s_id'];

            if (! isset($result[$groupName]) || $sId < $result[$groupName]) {
                $result[$groupName] = $sId; // s_id ต่ำ = access สูง (1=read, 2=edit)
            }
        }

        // group ที่ไม่มี → Denied
        foreach ($permMap as $groupName => $_) {
            if (! isset($result[$groupName])) {
                $result[$groupName] = 3; // Denied
            }
        }

        return $result;
    }

    // ── User Helpers ─────────────────────────────────────────────────

    private function getUserId(string $username): ?int
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT user_id FROM UserMgnt_VerifyUser WHERE username = ?"
        );
        $stmt->execute([strtolower($username)]);
        $row = $stmt->fetch();
        return $row ? (int) $row['user_id'] : null;
    }

    // ── Interface Methods ────────────────────────────────────────────

    public function getAvailablePermissions(): array
    {
        $this->system->loadMissing('permissions');
        return $this->system->permissions
            ->sortBy('sort_order')
            ->map(fn($p) => [
                'key'   => $p->key,
                'label' => $p->label,
                'group' => $p->group,
            ])
            ->values()
            ->all();
    }

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        $username = strtolower($user->username);

        try {
            $pdo     = $this->getConnection();
            $pgIdMap = $this->getPgIdMap();
            $permMap = $this->buildPermMap();

            // ตรวจสอบว่า user มีใน earth ไหม
            $userId = $this->getUserId($username);
            if (! $userId) {
                Log::info("[earth] User {$username} ยังไม่มีใน earth → กำลังสร้าง...");
                if (! $this->createUser($user, $permissions)) {
                    return false;
                }
                Log::info("[earth] สร้าง user {$username} สำเร็จ");
                return true;
            }

            // คำนวณ s_id ที่ต้องการสำหรับแต่ละ group
            $grantMap = $this->resolveGrantMap($permissions, $permMap);

            foreach ($grantMap as $groupName => $sId) {
                $pgId = $pgIdMap[$groupName] ?? null;
                if ($pgId === null) {
                    Log::warning("[earth] ไม่พบ pg_id สำหรับ group '{$groupName}'");
                    continue;
                }

                // ตรวจสอบว่ามี grant row อยู่แล้วไหม
                $check = $pdo->prepare(
                    "SELECT grant_id FROM UserMgnt_UserGrant WHERE user_id = ? AND pg_id = ?"
                );
                $check->execute([$userId, $pgId]);
                $existing = $check->fetchColumn();

                if ($existing) {
                    $pdo->prepare(
                        "UPDATE UserMgnt_UserGrant SET s_id = ?, modifyDate = GETDATE(), ModifyBy = 'UCM' WHERE grant_id = ?"
                    )->execute([$sId, $existing]);
                } else {
                    $pdo->prepare(
                        "INSERT INTO UserMgnt_UserGrant(user_id, pg_id, s_id, CreateDate, CreateBy, modifyDate, ModifyBy)
                         VALUES (?, ?, ?, GETDATE(), 'UCM', GETDATE(), 'UCM')"
                    )->execute([$userId, $pgId, $sId]);
                }
            }

            Log::info("[earth] syncPermissions {$username} — " . count($grantMap) . " groups updated");
            return true;

        } catch (PDOException $e) {
            Log::error("[earth] syncPermissions failed for {$user->username}: " . $e->getMessage() . " | file:" . $e->getFile() . ":" . $e->getLine());
            return false;
        }
    }

    public function getCurrentPermissions(UcmUser $user): array
    {
        $username = strtolower($user->username);

        try {
            $pdo    = $this->getConnection();
            $permMap = $this->buildPermMap();

            // กลับ map: group_name+s_id → ucm_key
            $reverse = [];
            foreach ($permMap as $groupName => $sIdMap) {
                foreach ($sIdMap as $sId => $key) {
                    $reverse[$groupName][$sId] = $key;
                }
            }

            $stmt = $pdo->prepare(
                "SELECT pg.group_name, ug.s_id
                 FROM UserMgnt_UserGrant ug
                 JOIN UserMgnt_PageGroup pg ON pg.pg_id = ug.pg_id
                 JOIN UserMgnt_VerifyUser uv ON uv.user_id = ug.user_id
                 WHERE uv.username = ?"
            );
            $stmt->execute([$username]);
            $rows = $stmt->fetchAll();

            $result = [];
            foreach ($rows as $row) {
                $groupName = trim($row['group_name']);
                $sId       = (int) $row['s_id'];
                $key       = $reverse[$groupName][$sId] ?? null;
                if ($key) {
                    $result[] = $key;
                }
            }
            return $result;

        } catch (PDOException $e) {
            Log::error("[earth] getCurrentPermissions failed for {$user->username}: " . $e->getMessage());
            return [];
        }
    }

    public function createUser(UcmUser $user, array $permissions): bool
    {
        $username = strtolower($user->username);

        try {
            $pdo  = $this->getConnection();

            // ตรวจซ้ำ
            if ($this->getUserId($username)) {
                return true;
            }

            // MSSQL: รวม INSERT + SCOPE_IDENTITY() ใน statement เดียวเพื่อดึง identity ที่สร้างใหม่
            // ใช้ GETDATE() แทนการส่ง datetime string เพื่อหลีกเลี่ยงปัญหา date format
            $stmt = $pdo->prepare(
                "INSERT INTO UserMgnt_VerifyUser(username, employee_no, department, position, status, createDate, createBy, modifyDate, modifyBy)
                 VALUES (?, ?, ?, ?, 1, GETDATE(), 'UCM', GETDATE(), 'UCM');
                 SELECT SCOPE_IDENTITY() AS user_id;"
            );
            $stmt->execute([
                $username,
                $user->employee_number ?? '',
                $user->department      ?? '',
                $user->title           ?? '',
            ]);
            $stmt->nextRowset();
            $row    = $stmt->fetch();
            $userId = $row ? (int) $row['user_id'] : 0;

            // fallback: query โดยตรงถ้า SCOPE_IDENTITY คืน null
            if (! $userId) {
                $userId = $this->getUserId($username);
            }
            if (! $userId) {
                Log::error("[earth] createUser: ไม่สามารถดึง user_id หลัง INSERT สำหรับ {$username}");
                return false;
            }

            // สร้าง grant rows สำหรับทุก group
            $pgIdMap  = $this->getPgIdMap();
            $permMap  = $this->buildPermMap();
            $grantMap = $this->resolveGrantMap($permissions, $permMap);

            foreach ($grantMap as $groupName => $sId) {
                $pgId = $pgIdMap[$groupName] ?? null;
                if ($pgId === null) {
                    continue;
                }
                $pdo->prepare(
                    "INSERT INTO UserMgnt_UserGrant(user_id, pg_id, s_id, CreateDate, CreateBy, modifyDate, ModifyBy)
                     VALUES (?, ?, ?, GETDATE(), 'UCM', GETDATE(), 'UCM')"
                )->execute([$userId, $pgId, $sId]);
            }

            Log::info("[earth] Created user {$username} with " . count($grantMap) . " grants");
            return true;

        } catch (PDOException $e) {
            Log::error("[earth] createUser failed for {$user->username}: " . $e->getMessage());
            return false;
        }
    }

    public function revokeAll(UcmUser $user): bool
    {
        $username = strtolower($user->username);

        try {
            $pdo    = $this->getConnection();
            $userId = $this->getUserId($username);
            if (! $userId) {
                return true; // ไม่มี user = ไม่มีอะไรต้อง revoke
            }

            $pdo->prepare(
                "UPDATE UserMgnt_UserGrant SET s_id = 3, modifyDate = GETDATE(), ModifyBy = 'UCM'
                 WHERE user_id = ?"
            )->execute([$userId]);

            Log::info("[earth] revokeAll {$username} — all grants set to Denied");
            return true;

        } catch (PDOException $e) {
            Log::error("[earth] revokeAll failed for {$user->username}: " . $e->getMessage());
            return false;
        }
    }

    public function getSystemUsers(): array
    {
        try {
            $rows = $this->getConnection()
                ->query(
                    "SELECT username, employee_no, department, position, status
                     FROM UserMgnt_VerifyUser
                     WHERE username IS NOT NULL AND username <> ''
                     ORDER BY username"
                )
                ->fetchAll();

            $ucmUsernames = DB::table('ucm_users')
                ->whereNull('deleted_at')
                ->pluck('username')
                ->map('strtolower')
                ->flip()
                ->all();

            $result = [];
            foreach ($rows as $row) {
                $username = strtolower(trim($row['username']));
                if ($username === '') {
                    continue;
                }
                $result[] = [
                    'username'   => $username,
                    'name'       => $username,
                    'email'      => '',
                    'department' => $row['department'] ?? '',
                    'status'     => (bool) ($row['status'] ?? 0),
                    'in_ucm'     => isset($ucmUsernames[$username]),
                ];
            }
            return $result;

        } catch (PDOException $e) {
            Log::error("[earth] getSystemUsers failed: " . $e->getMessage());
            return [];
        }
    }

    public function getAccountStatus(UcmUser $user): ?bool
    {
        try {
            $stmt = $this->getConnection()->prepare(
                "SELECT status FROM UserMgnt_VerifyUser WHERE username = ?"
            );
            $stmt->execute([strtolower($user->username)]);
            $row = $stmt->fetch();
            return $row ? (bool) $row['status'] : null;
        } catch (PDOException $e) {
            Log::error("[earth] getAccountStatus failed: " . $e->getMessage());
            return null;
        }
    }

    public function setAccountStatus(UcmUser $user, bool $active): bool
    {
        try {
            $this->getConnection()->prepare(
                "UPDATE UserMgnt_VerifyUser SET status = ?, modifyDate = GETDATE(), modifyBy = 'UCM' WHERE username = ?"
            )->execute([$active ? 1 : 0, strtolower($user->username)]);

            Log::info("[earth] setAccountStatus {$user->username} → " . ($active ? 'active' : 'disabled'));
            return true;
        } catch (PDOException $e) {
            Log::error("[earth] setAccountStatus failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Scan earth DB → ค้นหา group_name ใน UserMgnt_PageGroup ที่ยังไม่มีใน UCM
     * → สร้าง system_permissions ใน UCM อัตโนมัติ (read + edit)
     */
    public function discoverPermissions(): array
    {
        $created = [];

        try {
            $rows = $this->getConnection()
                ->query("SELECT group_name FROM UserMgnt_PageGroup ORDER BY priority, pg_id")
                ->fetchAll();

            $this->system->loadMissing('permissions');
            $existingGroups = $this->system->permissions
                ->pluck('group')
                ->unique()
                ->all();

            $sortBase = $this->system->permissions->max('sort_order') ?: 100;

            foreach ($rows as $i => $row) {
                $groupName = trim($row['group_name']);
                if (in_array($groupName, $existingGroups, true)) {
                    continue;
                }

                $slug    = preg_replace('/[^a-z0-9]/', '_', strtolower($groupName));
                $base    = $sortBase + ($i * 10);

                $editKey = $slug . '_edit';
                $readKey = $slug . '_read';
                $denyKey = $slug . '_deny';

                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $editKey],
                    ['label' => "{$groupName} — แก้ไข", 'group' => $groupName, 'remote_value' => "{$groupName}:2", 'is_exclusive' => true, 'sort_order' => $base]
                );
                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $readKey],
                    ['label' => "{$groupName} — ดูได้",  'group' => $groupName, 'remote_value' => "{$groupName}:1", 'is_exclusive' => true, 'sort_order' => $base + 1]
                );
                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $denyKey],
                    ['label' => "{$groupName} — Denied", 'group' => $groupName, 'remote_value' => "{$groupName}:3", 'is_exclusive' => true, 'sort_order' => $base + 2]
                );

                $created[] = $editKey;
                $created[] = $readKey;
                $created[] = $denyKey;
                Log::info("[earth] Discovered group: {$groupName} → {$editKey}, {$readKey}, {$denyKey}");
            }

        } catch (PDOException $e) {
            Log::error("[earth] discoverPermissions failed: " . $e->getMessage());
        }

        return $created;
    }
}
