<?php

namespace App\Adapters;

use App\Models\UcmUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

/**
 * Adapter สำหรับ repair-system
 *
 * repair-system ใช้ SQL Server (BFSAPPSDB03) และมี permission แบบ:
 *   - users.role  →  'user' | 'admin' | 'superadd'
 *   - ระบุ user ด้วย objectguid (ตรงกับ LDAP)
 *
 * Permission keys ที่ UCM ใช้:
 *   role_user     → SET users.role = 'user'
 *   role_admin    → SET users.role = 'admin'
 *   role_superadd → SET users.role = 'superadd'
 */
class RepairSystemAdapter extends BaseAdapter
{
    protected function getDriver(): string
    {
        return 'sqlsrv';
    }

    public function getAvailablePermissions(): array
    {
        return [
            ['key' => 'role_user',     'label' => 'ผู้ใช้งานทั่วไป',  'group' => 'Role', 'description' => 'ดูได้เฉพาะ Ticket ของตัวเอง'],
            ['key' => 'role_admin',    'label' => 'ผู้ดูแลระบบ',       'group' => 'Role', 'description' => 'ดู Ticket ทั้งหมด, มอบหมายงาน'],
            ['key' => 'role_superadd', 'label' => 'Super Admin',       'group' => 'Role', 'description' => 'สิทธิ์สูงสุด'],
        ];
    }

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        if (! $user->ldap_guid) {
            Log::warning("[repair-system] User {$user->username} ไม่มี ldap_guid ไม่สามารถ sync ได้");
            return false;
        }

        // แปลง UUID string → hex string ที่ repair-system เก็บ (ย้อน little-endian กลับ)
        $hexGuid = $this->uuidToHexString($user->ldap_guid);
        if (! $hexGuid) {
            Log::warning("[repair-system] ldap_guid ของ {$user->username} รูปแบบไม่ถูกต้อง: {$user->ldap_guid}");
            return false;
        }

        // หา role จาก permissions ที่ได้รับ (ใช้ role สูงสุดที่ได้รับ)
        $role = $this->resolveRole($permissions);

        try {
            $pdo = $this->getConnection();

            // ตรวจสอบว่า user มีอยู่ใน repair-system หรือยัง
            $stmt = $pdo->prepare("SELECT id FROM users WHERE objectguid = ?");
            $stmt->execute([$hexGuid]);
            $exists = $stmt->fetchColumn();

            if (! $exists) {
                // ยังไม่มีใน repair-system → สร้าง account อัตโนมัติ
                Log::info("[repair-system] User {$user->username} ยังไม่มี → กำลังสร้าง account...");
                if (! $this->createUser($user, $permissions)) {
                    return false;
                }
                Log::info("[repair-system] สร้าง account {$user->username} สำเร็จ");
                return true;
            }

            // อัปเดต role และ employee_number
            $update = $pdo->prepare("UPDATE users SET role = ?, employee_number = ?, fullname = ?, department = ?, email = ? WHERE objectguid = ?");
            $update->execute([
                $role,
                $user->employee_number ?? '',
                $user->name,
                $user->department ?? '',
                $user->email ?? '',
                $hexGuid,
            ]);

            Log::info("[repair-system] Synced {$user->username} → role='{$role}' (guid={$hexGuid})");
            return true;
        } catch (PDOException $e) {
            Log::error("[repair-system] Sync failed for {$user->username}: " . $e->getMessage());
            return false;
        }
    }

    public function getCurrentPermissions(UcmUser $user): array
    {
        if (! $user->ldap_guid) {
            return [];
        }

        $hexGuid = $this->uuidToHexString($user->ldap_guid);
        if (! $hexGuid) {
            return [];
        }

        try {
            $pdo  = $this->getConnection();
            $stmt = $pdo->prepare("SELECT role FROM users WHERE objectguid = ?");
            $stmt->execute([$hexGuid]);
            $row = $stmt->fetch();

            if (! $row) {
                return [];
            }

            return ['role_' . $row['role']]; // เช่น 'role_admin'

        } catch (PDOException $e) {
            Log::error("[repair-system] getCurrentPermissions failed: " . $e->getMessage());
            return [];
        }
    }

    public function revokeAll(UcmUser $user): bool
    {
        // ลด role กลับเป็น 'user' (ไม่ลบ user ออกจากระบบ)
        return $this->syncPermissions($user, ['role_user']);
    }

    public function getSystemUsers(): array
    {
        try {
            $rows = $this->getConnection()
                ->query("SELECT username, fullname, email, department, status FROM users ORDER BY fullname")
                ->fetchAll();

            // โหลด UCM usernames (lowercase) สำหรับเทียบ
            $ucmUsernames = DB::table('ucm_users')->whereNull('deleted_at')
                ->pluck('username')->map('strtolower')->flip()->all();

            return array_map(function ($row) use ($ucmUsernames) {
                $lower = strtolower($row['username'] ?? '');
                return [
                    'username'   => $row['username'] ?? '',
                    'name'       => $row['fullname'] ?? $row['username'] ?? '',
                    'email'      => $row['email'] ?? '',
                    'department' => $row['department'] ?? '',
                    'status'     => (bool) ($row['status'] ?? 1),
                    'in_ucm'     => isset($ucmUsernames[$lower]),
                ];
            }, $rows);

        } catch (\PDOException $e) {
            Log::error("[repair-system] getSystemUsers failed: " . $e->getMessage());
            return [];
        }
    }

    public function getAccountStatus(UcmUser $user): ?bool
    {
        $hexGuid = $this->uuidToHexString($user->ldap_guid ?? '');
        if (! $hexGuid) return null;

        try {
            $stmt = $this->getConnection()->prepare("SELECT status FROM users WHERE objectguid = ?");
            $stmt->execute([$hexGuid]);
            $row = $stmt->fetch();
            return $row ? (bool) $row['status'] : null;
        } catch (\PDOException $e) {
            Log::error("[repair-system] getAccountStatus failed: " . $e->getMessage());
            return null;
        }
    }

    public function setAccountStatus(UcmUser $user, bool $active): bool
    {
        $hexGuid = $this->uuidToHexString($user->ldap_guid ?? '');
        if (! $hexGuid) return false;

        try {
            $stmt = $this->getConnection()->prepare("UPDATE users SET status = ? WHERE objectguid = ?");
            $stmt->execute([$active ? 1 : 0, $hexGuid]);
            Log::info("[repair-system] setAccountStatus {$user->username} → " . ($active ? 'active' : 'disabled'));
            return true;
        } catch (\PDOException $e) {
            Log::error("[repair-system] setAccountStatus failed: " . $e->getMessage());
            return false;
        }
    }

    public function createUser(UcmUser $user, array $permissions): bool
    {
        if (! $user->ldap_guid) {
            Log::warning("[repair-system] createUser: {$user->username} ไม่มี ldap_guid");
            return false;
        }

        $hexGuid = $this->uuidToHexString($user->ldap_guid);
        if (! $hexGuid) {
            return false;
        }

        $role = $this->resolveRole($permissions);
        $now  = now()->format('Y-m-d H:i:s.000');

        try {
            $pdo = $this->getConnection();

            // ตรวจซ้ำกัน race condition
            $check = $pdo->prepare("SELECT id FROM users WHERE objectguid = ?");
            $check->execute([$hexGuid]);
            if ($check->fetchColumn()) {
                return true; // มีแล้ว ไม่ต้องสร้าง
            }

            $stmt = $pdo->prepare("
                INSERT INTO users
                    (objectguid, employee_number, fullname, department, email, username, password, role,
                     status, created_at, updated_at, user_agent, platform, ip_address, device_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, 'UCM', 'UCM', '0.0.0.0', 'UCM')
                SELECT SCOPE_IDENTITY()
            ");

            $stmt->execute([
                $hexGuid,
                $user->employee_number ?? '', // employee_number
                $user->name,
                $user->department ?? '',
                $user->email ?? '',
                strtolower($user->username),
                password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT), // random password (ใช้ LDAP login)
                $role,
                $now,
                $now,
            ]);

            Log::info("[repair-system] Created account {$user->username} role='{$role}'");
            return true;

        } catch (PDOException $e) {
            Log::error("[repair-system] createUser failed for {$user->username}: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * เลือก role สูงสุดจาก permission keys
     * Priority: superadd > admin > user
     */
    protected function resolveRole(array $permissions): string
    {
        // Priority map — ลำดับสำคัญ: สูงสุดก่อน
        // เพิ่ม role ใหม่ที่นี่ที่เดียว แล้ว restart queue worker
        $priority = ['role_superadd', 'role_admin', 'role_agent', 'role_user'];

        foreach ($priority as $key) {
            if (in_array($key, $permissions)) {
                return str_replace('role_', '', $key);
            }
        }

        return 'user';
    }

    /**
     * แปลง UUID string (จาก UCM) กลับเป็น hex string 32 ตัว
     * ที่ repair-system เก็บใน objectguid column (VARCHAR)
     *
     * UCM เก็บ (little-endian decoded UUID):
     *   d5356709-02b7-4920-b57a-1b971aefadb8
     *
     * repair-system เก็บ (raw hex ของ binary objectGUID):
     *   096735d5b7022049b57a1b971aefadb8
     *
     * การแปลง: ย้อน little-endian กลับ 3 ส่วนแรก แล้ว concatenate
     */
    protected function uuidToHexString(string $uuid): ?string
    {
        $hex = str_replace('-', '', $uuid);

        if (strlen($hex) !== 32) {
            return null;
        }

        // ย้อน little-endian กลับ (reverse bytes สำหรับ 3 ส่วนแรก)
        $part1 = substr($hex, 6, 2) . substr($hex, 4, 2) . substr($hex, 2, 2) . substr($hex, 0, 2);
        $part2 = substr($hex, 10, 2) . substr($hex, 8, 2);
        $part3 = substr($hex, 14, 2) . substr($hex, 12, 2);
        $part4 = substr($hex, 16, 4);
        $part5 = substr($hex, 20, 12);

        return $part1 . $part2 . $part3 . $part4 . $part5;
    }
}
