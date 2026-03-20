<?php

namespace App\Adapters;

use App\Models\System;
use App\Models\UcmUser;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

/**
 * Base class ที่มี helper methods สำหรับ Adapter ที่ใช้ DB connection โดยตรง
 */
abstract class BaseAdapter implements SystemAdapterInterface
{
    protected System $system;
    protected ?PDO $pdo = null;

    public function __construct(System $system)
    {
        $this->system = $system;
    }

    /**
     * สร้าง PDO connection ไปยังฐานข้อมูลของระบบเดิม
     * Support: mysql, sqlsrv (SQL Server)
     */
    protected function getConnection(): PDO
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        $driver   = $this->getDriver();
        $host     = $this->system->db_host;
        $port     = $this->system->db_port ?: 1433;
        $dbname   = $this->system->db_name;
        $username = $this->system->db_user;
        $password = $this->system->db_password;

        if ($driver === 'sqlsrv') {
            $dsn = "sqlsrv:Server={$host},{$port};Database={$dbname};TrustServerCertificate=1;Encrypt=0";
        } else {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // PDO::ATTR_TIMEOUT ไม่รองรับใน sqlsrv driver
        if ($driver !== 'sqlsrv') {
            $options[PDO::ATTR_TIMEOUT] = 10;
        }

        $this->pdo = new PDO($dsn, $username, $password, $options);

        return $this->pdo;
    }

    protected function getDriver(): string
    {
        return 'mysql'; // override ใน subclass ถ้าใช้ sqlsrv
    }

    /**
     * Default: ไม่รองรับ auto-create
     * Override ใน subclass เพื่อเปิดใช้งาน
     */
    public function createUser(UcmUser $user, array $permissions): bool
    {
        return false;
    }

    public function getSystemUsers(): array
    {
        return [];
    }

    public function discoverPermissions(): array
    {
        return []; // override ใน subclass ที่รองรับ
    }

    public function getAccountStatus(UcmUser $user): ?bool
    {
        return null; // override ใน subclass
    }

    public function setAccountStatus(UcmUser $user, bool $active): bool
    {
        return false; // override ใน subclass
    }

    public function testConnection(): array
    {
        try {
            $pdo = $this->getConnection();
            $pdo->query('SELECT 1');
            return ['ok' => true, 'message' => 'เชื่อมต่อสำเร็จ'];
        } catch (PDOException $e) {
            Log::warning("[{$this->system->slug}] Connection test failed: " . $e->getMessage());
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Default: ระบบนี้ไม่มี permission definition table
     * Override ใน subclass ที่มีตารางของตัวเอง
     */
    public function provisionPermission(string $key, string $label, string $group): string|int|null
    {
        return null;
    }

    /**
     * Default: ไม่ต้องลบอะไรในระบบภายนอก
     * Override ใน subclass ที่มี permission definition table
     */
    public function deletePermission(string $remoteValue): bool
    {
        return true;
    }

    /** Default: ไม่รองรับ 2-way permission sync — override ใน adapter ที่ provision + delete permission จริง */
    public function supports2WayPermissions(): bool
    {
        return false;
    }

    public function getManagedGroups(): array
    {
        return [];
    }

    public function getGroupSchema(string $group): array
    {
        return [];
    }

    public function getGroupRecords(string $group): array
    {
        return [];
    }

    public function addGroupRecord(string $group, string $name, array $extra = []): array|false
    {
        return false;
    }

    public function updateGroupRecord(string $group, int $id, string $name, array $extra = []): bool
    {
        return false;
    }

    public function deleteGroupRecord(string $group, int $id): bool
    {
        return false;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}
