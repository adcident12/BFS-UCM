<?php

namespace App\Adapters;

use App\Enums\PermissionDeleteMode;
use App\Models\ConnectorConfig;
use App\Models\System;
use App\Models\SystemPermission;
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

        try {
            $this->pdo = new PDO($dsn, $cfg->db_user, $cfg->db_password, $options);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] Connection failed: " . $e->getMessage());
            throw $e;
        }

        return $this->pdo;
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function resolveUserIdentifier(UcmUser $user): string
    {
        return $this->config->user_ucm_identifier === 'employee_number'
            ? (string) $user->employee_number
            : $user->username;
    }

    /**
     * คืนค่าที่จะใช้ใน WHERE / INSERT ของ junction table
     *
     * ถ้า user_pk_col ตั้งค่าไว้ → perm_user_fk_col อ้างอิง PK (INT) ของ user_table
     * ต้อง lookup ก่อน: SELECT {pk_col} FROM {user_table} WHERE {identifier_col} = {identifier}
     *
     * ถ้าไม่ตั้งค่า → ใช้ identifier (username/employee_number) โดยตรง
     */
    private function resolveUserFkValue(UcmUser $user): string
    {
        $cfg        = $this->config;
        $identifier = $this->resolveUserIdentifier($user);

        if (! filled($cfg->user_pk_col)) {
            return $identifier;
        }

        try {
            $pdo       = $this->getConnection();
            $userTable = $this->quoteIdentifier($cfg->user_table);
            $idCol     = $this->quoteIdentifier($cfg->user_identifier_col);
            $pkCol     = $this->quoteIdentifier($cfg->user_pk_col);

            $stmt = $pdo->prepare("SELECT {$pkCol} FROM {$userTable} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $pk = $stmt->fetchColumn();

            return $pk !== false ? (string) $pk : $identifier;
        } catch (PDOException $e) {
            Log::warning("[DynamicAdapter:{$this->system->slug}] resolveUserFkValue fallback to identifier: ".$e->getMessage());

            return $identifier;
        }
    }

    // ── Composite Junction Helpers ─────────────────────────────────────────

    private function isComposite(): bool
    {
        return ! empty($this->config->perm_composite_cols);
    }

    private function buildCompositeKey(array $row): string
    {
        $parts = [(string) ($row[$this->config->perm_value_col] ?? '')];
        foreach ($this->config->perm_composite_cols ?? [] as $cc) {
            $parts[] = (string) ($row[$cc['col']] ?? '');
        }

        return implode(':', $parts);
    }

    private function parseCompositeKey(string $key): array
    {
        $compositeCols = $this->config->perm_composite_cols ?? [];
        $count         = 1 + count($compositeCols);
        $parts         = explode(':', $key, $count);
        $result        = [$this->config->perm_value_col => $parts[0] ?? ''];
        foreach ($compositeCols as $i => $cc) {
            $result[$cc['col']] = $parts[$i + 1] ?? '';
        }

        return $result;
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
            $pdo    = $this->getConnection();
            $table  = $this->quoteIdentifier($cfg->perm_table);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            if ($this->isComposite()) {
                $compositeCols = $cfg->perm_composite_cols;
                $extraQuoted   = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                $allCols       = array_merge([$valCol], $extraQuoted);
                $stmt          = $pdo->query('SELECT DISTINCT '.implode(', ', $allCols)." FROM {$table}");
                $rows          = $stmt->fetchAll();

                return array_map(function ($row) use ($cfg, $compositeCols) {
                    $key        = $this->buildCompositeKey($row);
                    $labelParts = [(string) ($row[$cfg->perm_value_col] ?? '')];
                    foreach ($compositeCols as $cc) {
                        $labelParts[] = (string) ($row[$cc['col']] ?? '');
                    }
                    $label = implode(' · ', array_filter($labelParts, fn ($v) => $v !== ''));

                    return ['key' => $key, 'label' => $label, 'group' => 'ทั่วไป'];
                }, $rows);
            }

            $cols = [$valCol];
            if ($cfg->perm_label_col) {
                $cols[] = $this->quoteIdentifier($cfg->perm_label_col);
            }
            if ($cfg->perm_group_col) {
                $cols[] = $this->quoteIdentifier($cfg->perm_group_col);
            }

            $stmt = $pdo->query('SELECT DISTINCT '.implode(', ', $cols)." FROM {$table}");
            $rows = $stmt->fetchAll();

            return array_map(function ($row) use ($cfg) {
                return [
                    'key'   => (string) $row[$cfg->perm_value_col],
                    'label' => $cfg->perm_label_col ? ($row[$cfg->perm_label_col] ?? '') : $row[$cfg->perm_value_col],
                    'group' => $cfg->perm_group_col ? ($row[$cfg->perm_group_col] ?? 'ทั่วไป') : 'ทั่วไป',
                ];
            }, $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getAvailablePermissions: ".$e->getMessage());

            return [];
        }
    }

    public function getCurrentPermissions(UcmUser $user): array
    {
        $cfg        = $this->config;
        $identifier = $this->resolveUserFkValue($user);

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

            if ($this->isComposite()) {
                $compositeCols = $cfg->perm_composite_cols;
                $extraQuoted   = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                $allCols       = array_merge([$valCol], $extraQuoted);
                $stmt          = $pdo->prepare('SELECT '.implode(', ', $allCols)." FROM {$table} WHERE {$fkCol} = ?");
                $stmt->execute([$identifier]);

                return array_map(fn ($row) => $this->buildCompositeKey($row), $stmt->fetchAll());
            }

            $stmt = $pdo->prepare("SELECT {$valCol} FROM {$table} WHERE {$fkCol} = ?");
            $stmt->execute([$identifier]);

            return array_column($stmt->fetchAll(), $cfg->perm_value_col);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getCurrentPermissions: ".$e->getMessage());

            return [];
        }
    }

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        $cfg        = $this->config;
        $identifier = $this->resolveUserFkValue($user);

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

            if ($this->isComposite()) {
                $compositeCols  = $cfg->perm_composite_cols;
                $extraQuoted    = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                $allInsertCols  = array_merge([$fkCol, $valCol], $extraQuoted);
                $placeholders   = implode(', ', array_fill(0, count($allInsertCols), '?'));
                $pdo->beginTransaction();
                $del = $pdo->prepare("DELETE FROM {$table} WHERE {$fkCol} = ?");
                $del->execute([$identifier]);
                if (! empty($permissions)) {
                    $ins = $pdo->prepare('INSERT INTO '.$table.' ('.implode(', ', $allInsertCols).') VALUES ('.$placeholders.')');
                    foreach ($permissions as $permKey) {
                        $parsed = $this->parseCompositeKey($permKey);
                        $vals   = [$identifier, $parsed[$cfg->perm_value_col] ?? ''];
                        foreach ($compositeCols as $cc) {
                            $vals[] = $parsed[$cc['col']] ?? '';
                        }
                        $ins->execute($vals);
                    }
                }
                $pdo->commit();

                return true;
            }

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
                    ? ((string) $statusVal === (string) $cfg->user_status_active_val)
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

    // ── 2-Way Sync ─────────────────────────────────────────────────────────

    /** รองรับ 2-way เมื่อมีการกำหนด perm_def_table ใน ConnectorConfig */
    public function supports2WayPermissions(): bool
    {
        return filled($this->config->perm_def_table);
    }

    /**
     * อ่าน permission ทั้งหมดจาก remote DB แล้ว sync เข้า system_permissions
     *
     * - manual mode        : ใช้ manual_permissions จาก config
     * - junction/column + perm_def_table : อ่าน perm_def_table (กรอง soft-delete ออก)
     * - junction/column ธรรมดา           : อ่าน distinct values จาก perm_table
     *
     * @return string[] รายการ key ที่เพิ่งสร้างใหม่
     */
    public function discoverPermissions(): array
    {
        $cfg     = $this->config;
        $created = [];

        try {
            $this->system->loadMissing('permissions');
            $existingKeys = $this->system->permissions->pluck('key')->all();

            $rows = $this->fetchPermissionRows();

            foreach ($rows as $row) {
                $key         = trim((string) ($row['key'] ?? ''));
                $remoteValue = isset($row['remote_value']) && $row['remote_value'] !== '' ? $row['remote_value'] : null;

                if ($key === '') {
                    continue;
                }

                if (in_array($key, $existingKeys, true)) {
                    // อัปเดต remote_value ของ record เดิมที่ยังว่างอยู่ (backfill)
                    if ($remoteValue !== null) {
                        SystemPermission::where('system_id', $this->system->id)
                            ->where('key', $key)
                            ->whereNull('remote_value')
                            ->update(['remote_value' => $remoteValue]);
                    }

                    continue;
                }

                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $key],
                    [
                        'label'        => $row['label'] ?? $key,
                        'group'        => $row['group'] ?? null,
                        'remote_value' => $remoteValue,
                    ]
                );

                $existingKeys[] = $key;
                $created[]      = $key;

                Log::info("[DynamicAdapter:{$this->system->slug}] discoverPermissions: สร้าง '{$key}'");
            }

            // Cleanup: เมื่อใช้ perm_def_table ให้ลบ UCM permissions ที่ไม่อยู่ใน
            // active list อีกต่อไป (ถูก soft-delete หรือลบออกจาก external DB แล้ว)
            // ทำเฉพาะเมื่อ $rows มีผลลัพธ์ หรือเมื่อ soft-delete config ถูกตั้งค่าไว้
            // (ป้องกันลบ UCM ทั้งหมดกรณี connection error ที่ส่ง empty silently)
            if (filled($cfg->perm_def_table) && filled($cfg->perm_def_value_col)) {
                $activeKeys = array_values(array_filter(
                    array_map(fn ($r) => trim((string) ($r['key'] ?? '')), $rows),
                    fn ($k) => $k !== ''
                ));

                $stalePermissions = SystemPermission::where('system_id', $this->system->id)
                    ->whereNotNull('remote_value')
                    ->when(! empty($activeKeys), fn ($q) => $q->whereNotIn('key', $activeKeys))
                    ->when(empty($activeKeys), fn ($q) => $q->whereRaw('0')) // ถ้า active list ว่าง ไม่ลบ
                    ->get();

                foreach ($stalePermissions as $stale) {
                    $stale->delete();
                    Log::info("[DynamicAdapter:{$this->system->slug}] discoverPermissions: ลบ '{$stale->key}' (soft-deleted หรือไม่พบใน perm_def_table)");
                }
            }
        } catch (\Throwable $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] discoverPermissions failed: ".$e->getMessage());
        }

        return $created;
    }

    /**
     * @return array<int, array{key: string, label: string, group: string|null, remote_value: string|null}>
     */
    private function fetchPermissionRows(): array
    {
        $cfg = $this->config;

        // Manual mode — อ่านจาก config โดยตรง ไม่ต้องต่อ DB
        if ($cfg->permission_mode === 'manual') {
            return array_map(fn ($p) => [
                'key'          => $p['key']   ?? '',
                'label'        => $p['label'] ?? ($p['key'] ?? ''),
                'group'        => $p['group'] ?? null,
                'remote_value' => null,
            ], (array) ($cfg->manual_permissions ?? []));
        }

        $pdo = $this->getConnection();

        // มี perm_def_table → อ่าน canonical list (2-way sync)
        if (filled($cfg->perm_def_table) && filled($cfg->perm_def_value_col)) {
            $defTable = $this->quoteIdentifier($cfg->perm_def_table);
            $valCol   = $this->quoteIdentifier($cfg->perm_def_value_col);
            $pkCol    = $cfg->perm_def_pk_col ?: 'id';
            $quotedPk = $this->quoteIdentifier($pkCol);

            // เลือก PK เสมอเพื่อใช้เป็น remote_value
            $cols = [$quotedPk, $valCol];
            if (filled($cfg->perm_def_label_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_label_col);
            }
            if (filled($cfg->perm_def_group_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_group_col);
            }

            // กรอง soft-deleted rows ออก ด้วย soft_col != soft_val
            $where  = '';
            $params = [];
            if (filled($cfg->perm_def_soft_delete_col) && filled($cfg->perm_def_soft_delete_val)) {
                $softCol  = $this->quoteIdentifier($cfg->perm_def_soft_delete_col);
                $where    = " WHERE ({$softCol} IS NULL OR {$softCol} != ?)";
                $params[] = $cfg->perm_def_soft_delete_val;
            }

            $stmt = $pdo->prepare('SELECT '.implode(', ', $cols)." FROM {$defTable}{$where}");
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            return array_map(fn ($row) => [
                'key'          => (string) $row[$cfg->perm_def_value_col],
                'label'        => $cfg->perm_def_label_col ? ($row[$cfg->perm_def_label_col] ?? '') : $row[$cfg->perm_def_value_col],
                'group'        => $cfg->perm_def_group_col ? ($row[$cfg->perm_def_group_col] ?? null) : null,
                'remote_value' => (string) $row[$pkCol],
            ], $rows);
        }

        // ไม่มี perm_def_table → อ่าน distinct values จาก perm_table (ไม่มี PK ให้ track)
        return array_map(fn ($p) => [
            'key'          => $p['key'],
            'label'        => $p['label'] ?? $p['key'],
            'group'        => $p['group'] ?? null,
            'remote_value' => null,
        ], $this->getAvailablePermissions());
    }

    public function getPermissionDeleteMode(): PermissionDeleteMode
    {
        return PermissionDeleteMode::tryFrom($this->config->perm_delete_mode ?? '')
            ?? PermissionDeleteMode::DetachOnly;
    }

    /**
     * สร้าง permission definition ใน remote DB
     * INSERT ลง perm_def_table แล้วคืน PK หรือ key เป็น remote_value
     */
    public function provisionPermission(string $key, string $label, string $group): string|int|null
    {
        $cfg = $this->config;

        if (! $cfg->perm_def_table || ! $cfg->perm_def_value_col) {
            return null;
        }

        try {
            $pdo      = $this->getConnection();
            $defTable = $this->quoteIdentifier($cfg->perm_def_table);
            $valCol   = $this->quoteIdentifier($cfg->perm_def_value_col);
            $pkCol    = $cfg->perm_def_pk_col ?: 'id';
            $quotedPk = $this->quoteIdentifier($pkCol);

            // ถ้ามีอยู่แล้วให้คืน PK ที่มีอยู่ (idempotent)
            $check = $pdo->prepare("SELECT {$quotedPk} FROM {$defTable} WHERE {$valCol} = ?");
            $check->execute([$key]);
            if ($existing = $check->fetchColumn()) {
                return $existing;
            }

            // Build dynamic INSERT
            $cols = [$valCol];
            $vals = [$key];

            if ($cfg->perm_def_label_col && ! blank($label)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_label_col);
                $vals[] = $label;
            }

            if ($cfg->perm_def_group_col && ! blank($group)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_group_col);
                $vals[] = $group;
            }

            $placeholders = implode(', ', array_fill(0, count($cols), '?'));
            $pdo->prepare(
                'INSERT INTO '.$defTable.' ('.implode(', ', $cols).') VALUES ('.$placeholders.')'
            )->execute($vals);

            $insertId = $pdo->lastInsertId();

            Log::info("[DynamicAdapter:{$this->system->slug}] provisionPermission: สร้าง '{$key}' สำเร็จ");

            return $insertId ?: $key;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] provisionPermission: ".$e->getMessage());

            return null;
        }
    }

    /**
     * ลบ permission definition จาก remote DB ตาม delete mode ที่ตั้งค่าไว้
     *
     * Hard:       DELETE FROM def_table WHERE pk = remoteValue
     * Soft:       UPDATE def_table SET soft_col = soft_val WHERE pk = remoteValue
     * DetachOnly: ไม่ทำอะไร — ลบเฉพาะใน UCM
     */
    public function deletePermission(string $remoteValue): bool
    {
        $cfg  = $this->config;
        $mode = $this->getPermissionDeleteMode();

        if ($mode === PermissionDeleteMode::DetachOnly || ! $cfg->perm_def_table) {
            return true;
        }

        $pkCol    = $cfg->perm_def_pk_col ?: 'id';
        $defTable = $this->quoteIdentifier($cfg->perm_def_table);
        $quotedPk = $this->quoteIdentifier($pkCol);

        try {
            $pdo = $this->getConnection();

            if ($mode === PermissionDeleteMode::Hard) {
                $pdo->prepare("DELETE FROM {$defTable} WHERE {$quotedPk} = ?")
                    ->execute([$remoteValue]);

                Log::info("[DynamicAdapter:{$this->system->slug}] deletePermission (hard): ลบ '{$remoteValue}' สำเร็จ");
            } elseif ($mode === PermissionDeleteMode::Soft) {
                $softCol = $cfg->perm_def_soft_delete_col;
                $softVal = $cfg->perm_def_soft_delete_val ?? '1';

                if (! $softCol) {
                    return true;
                }

                $quotedSoftCol = $this->quoteIdentifier($softCol);
                $pdo->prepare("UPDATE {$defTable} SET {$quotedSoftCol} = ? WHERE {$quotedPk} = ?")
                    ->execute([$softVal, $remoteValue]);

                Log::info("[DynamicAdapter:{$this->system->slug}] deletePermission (soft): soft-deleted '{$remoteValue}' สำเร็จ");
            }

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] deletePermission: ".$e->getMessage());

            return false;
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
