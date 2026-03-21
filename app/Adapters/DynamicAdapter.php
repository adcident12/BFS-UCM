<?php

namespace App\Adapters;

use App\Models\ConnectorConfig;
use App\Models\System;
use App\Models\UcmUser;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

/**
 * Adapter ที่สร้างขึ้นแบบ Dynamic จาก ConnectorConfig
 * ไม่ต้องเขียนโค้ด — กำหนดผ่าน Connector Wizard ใน UI
 */
class DynamicAdapter extends BaseAdapter implements SystemAdapterInterface
{
    protected ConnectorConfig $config;

    public function __construct(System $system)
    {
        parent::__construct($system);

        $config = $system->relationLoaded('connectorConfig')
            ? $system->connectorConfig
            : ConnectorConfig::where('system_id', $system->id)->first();

        if (! $config) {
            throw new \RuntimeException(
                "ไม่พบ ConnectorConfig สำหรับระบบ '{$system->slug}' — กรุณาตั้งค่าผ่าน Connector Wizard"
            );
        }

        $this->config = $config;
    }

    // ── Connection ─────────────────────────────────────────────────────────

    protected function getConnection(): PDO
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        $cfg = $this->config;
        $dsn = match ($cfg->db_driver) {
            'sqlsrv' => "sqlsrv:Server={$cfg->db_host},{$cfg->db_port};Database={$cfg->db_name};TrustServerCertificate=1;Encrypt=0",
            'pgsql'  => "pgsql:host={$cfg->db_host};port={$cfg->db_port};dbname={$cfg->db_name}",
            default  => "mysql:host={$cfg->db_host};port={$cfg->db_port};dbname={$cfg->db_name};charset=utf8mb4",
        };

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($cfg->db_driver !== 'sqlsrv') {
            $options[PDO::ATTR_TIMEOUT] = 10;
        }

        $this->pdo = new PDO($dsn, $cfg->db_user, $cfg->db_password, $options);

        return $this->pdo;
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function resolveUserIdentifier(UcmUser $user): string
    {
        return $this->config->user_ucm_identifier === 'employee_number'
            ? (string) $user->employee_number
            : $user->username;
    }

    // ── Interface Implementation ───────────────────────────────────────────

    public function testConnection(): array
    {
        try {
            $pdo = $this->getConnection();
            $pdo->query('SELECT 1');
            return ['ok' => true, 'message' => 'เชื่อมต่อสำเร็จ'];
        } catch (PDOException $e) {
            Log::warning("[DynamicAdapter:{$this->system->slug}] testConnection failed: " . $e->getMessage());
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAvailablePermissions(): array
    {
        $cfg = $this->config;

        if ($cfg->permission_mode === 'manual') {
            return $cfg->manual_permissions ?? [];
        }

        if (! $cfg->perm_table || ! $cfg->perm_value_col) {
            return [];
        }

        try {
            $pdo   = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->perm_table);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            $cols = [$valCol];
            if ($cfg->perm_label_col) {
                $cols[] = $this->quoteIdentifier($cfg->perm_label_col);
            }
            if ($cfg->perm_group_col) {
                $cols[] = $this->quoteIdentifier($cfg->perm_group_col);
            }

            $stmt = $pdo->query("SELECT DISTINCT " . implode(', ', $cols) . " FROM {$table}");
            $rows = $stmt->fetchAll();

            return array_map(function ($row) use ($cfg) {
                return [
                    'key'   => (string) $row[$cfg->perm_value_col],
                    'label' => $cfg->perm_label_col ? ($row[$cfg->perm_label_col] ?? '') : $row[$cfg->perm_value_col],
                    'group' => $cfg->perm_group_col ? ($row[$cfg->perm_group_col] ?? 'ทั่วไป') : 'ทั่วไป',
                ];
            }, $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getAvailablePermissions: " . $e->getMessage());
            return [];
        }
    }

    public function getCurrentPermissions(UcmUser $user): array
    {
        $cfg        = $this->config;
        $identifier = $this->resolveUserIdentifier($user);

        if ($cfg->permission_mode === 'manual') {
            return [];
        }

        if (! $cfg->perm_table || ! $cfg->perm_value_col || ! $cfg->perm_user_fk_col) {
            return [];
        }

        try {
            $pdo    = $this->getConnection();
            $table  = $this->quoteIdentifier($cfg->perm_table);
            $fkCol  = $this->quoteIdentifier($cfg->perm_user_fk_col);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            $stmt = $pdo->prepare("SELECT {$valCol} FROM {$table} WHERE {$fkCol} = ?");
            $stmt->execute([$identifier]);

            return array_column($stmt->fetchAll(), $cfg->perm_value_col);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getCurrentPermissions: " . $e->getMessage());
            return [];
        }
    }

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        $cfg        = $this->config;
        $identifier = $this->resolveUserIdentifier($user);

        if ($cfg->permission_mode === 'manual') {
            return true; // manual mode — ไม่มี remote sync
        }

        if (! $cfg->perm_table || ! $cfg->perm_user_fk_col || ! $cfg->perm_value_col) {
            return false;
        }

        try {
            $pdo    = $this->getConnection();
            $table  = $this->quoteIdentifier($cfg->perm_table);
            $fkCol  = $this->quoteIdentifier($cfg->perm_user_fk_col);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            $pdo->beginTransaction();

            // ลบ permissions เดิมทั้งหมด
            $del = $pdo->prepare("DELETE FROM {$table} WHERE {$fkCol} = ?");
            $del->execute([$identifier]);

            // Insert permissions ใหม่
            if (! empty($permissions)) {
                $ins = $pdo->prepare("INSERT INTO {$table} ({$fkCol}, {$valCol}) VALUES (?, ?)");
                foreach ($permissions as $perm) {
                    $ins->execute([$identifier, $perm]);
                }
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->pdo?->inTransaction()) {
                $this->pdo->rollBack();
            }
            Log::error("[DynamicAdapter:{$this->system->slug}] syncPermissions: " . $e->getMessage());
            return false;
        }
    }

    public function revokeAll(UcmUser $user): bool
    {
        return $this->syncPermissions($user, []);
    }

    public function getSystemUsers(): array
    {
        $cfg = $this->config;

        if (! $cfg->user_table || ! $cfg->user_identifier_col) {
            return [];
        }

        try {
            $pdo   = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);

            $ucmUsers     = UcmUser::select('username', 'employee_number')->get();
            $ucmUsernames = $ucmUsers->pluck('username')->toArray();
            $ucmEmployees = $ucmUsers->pluck('employee_number')->filter()->toArray();

            $stmt = $pdo->query("SELECT * FROM {$table}");
            $rows = $stmt->fetchAll();

            return array_map(function ($row) use ($cfg, $ucmUsernames, $ucmEmployees) {
                $identifier = $row[$cfg->user_identifier_col] ?? '';
                $inUcm = in_array($identifier, $ucmUsernames, true)
                    || in_array($identifier, $ucmEmployees, true);

                $statusVal  = $cfg->user_status_col ? ($row[$cfg->user_status_col] ?? null) : null;
                $isActive   = $cfg->user_status_active_val !== null
                    ? ($statusVal == $cfg->user_status_active_val)
                    : true;

                return [
                    'username'   => $identifier,
                    'name'       => $cfg->user_name_col ? ($row[$cfg->user_name_col] ?? '') : $identifier,
                    'email'      => $cfg->user_email_col ? ($row[$cfg->user_email_col] ?? '') : '',
                    'department' => $cfg->user_dept_col ? ($row[$cfg->user_dept_col] ?? '') : '',
                    'status'     => $isActive,
                    'in_ucm'     => $inUcm,
                ];
            }, $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getSystemUsers: " . $e->getMessage());
            return [];
        }
    }

    // ── Utility ────────────────────────────────────────────────────────────

    /**
     * Quote an identifier (table/column name) safely for the current driver.
     * Only allows alphanumeric + underscore + dot.
     */
    private function quoteIdentifier(string $name): string
    {
        // Whitelist: only word characters and dots (schema.table)
        if (! preg_match('/^[\w.]+$/', $name)) {
            throw new \InvalidArgumentException("Invalid identifier: {$name}");
        }

        // MySQL → backtick  |  PostgreSQL + SQL Server → double-quote
        $quote = $this->config->db_driver === 'mysql' ? '`' : '"';

        return implode('.', array_map(
            fn ($part) => $quote . str_replace($quote, '', $part) . $quote,
            explode('.', $name)
        ));
    }
}
