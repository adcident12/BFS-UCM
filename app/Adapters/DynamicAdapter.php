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
            'pgsql' => "pgsql:host={$cfg->db_host};port={$cfg->db_port};dbname={$cfg->db_name}",
            default => "mysql:host={$cfg->db_host};port={$cfg->db_port};dbname={$cfg->db_name};charset=utf8mb4",
        };

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($cfg->db_driver !== 'sqlsrv') {
            $options[PDO::ATTR_TIMEOUT] = 10;
        }

        try {
            $this->pdo = new PDO($dsn, $cfg->db_user, $cfg->db_password, $options);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] Connection failed: ".$e->getMessage());
            throw $e;
        }

        return $this->pdo;
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function resolveUserIdentifier(UcmUser $user): string
    {
        if ($this->config->user_ucm_identifier === 'employee_number') {
            return filled($user->employee_number) ? (string) $user->employee_number : $user->username;
        }

        return $user->username;
    }

    /**
     * แปลง department ของ UCM user เป็น dept code ที่ระบบภายนอกใช้
     *
     * ถ้ามี dept_map config:  lookup UCM name → external code
     * ถ้าไม่มี / ไม่เจอ key: คืนค่าเดิม ($user->department) โดยตรง
     */
    private function resolveDeptValue(UcmUser $user): string
    {
        $dept = $user->department ?? '';
        $map = $this->config->dept_map ?? [];

        if (! empty($map) && isset($map[$dept])) {
            return (string) $map[$dept];
        }

        return $dept;
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
        $cfg = $this->config;
        $identifier = $this->resolveUserIdentifier($user);

        if (! filled($cfg->user_pk_col)) {
            return $identifier;
        }

        try {
            $pdo = $this->getConnection();
            $userTable = $this->primaryUserTable();
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $pkCol = $this->quoteIdentifier($cfg->user_pk_col);

            $stmt = $pdo->prepare("SELECT {$pkCol} FROM {$userTable} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $pk = $stmt->fetchColumn();

            return $pk !== false ? (string) $pk : $identifier;
        } catch (PDOException $e) {
            Log::warning("[DynamicAdapter:{$this->system->slug}] resolveUserFkValue fallback to identifier: ".$e->getMessage());

            return $identifier;
        }
    }

    // ── Multi-Table JOIN Helpers (Feature A) ──────────────────────────────

    /**
     * คืน FROM clause สำหรับ user table
     *
     * ถ้า user_tables ถูกตั้งค่า (Feature A) → สร้าง JOIN SQL จาก array
     * ถ้าไม่มี → fallback กลับเป็น single user_table เดิม
     *
     * @return array{sql: string, primary_alias: string|null}
     */
    private function buildUserJoinSql(): array
    {
        $cfg = $this->config;
        $tables = $cfg->user_tables;

        if (empty($tables) || count($tables) === 1) {
            return ['sql' => $this->quoteIdentifier($cfg->user_table), 'primary_alias' => null];
        }

        $parts = [];
        $primaryAlias = null;

        foreach ($tables as $i => $def) {
            $quoted = $this->quoteIdentifier($def['table'] ?? '');
            $alias = $def['alias'] ?? null;

            if ($i === 0) {
                $primaryAlias = $alias;
                $parts[] = $alias ? "{$quoted} {$alias}" : $quoted;

                continue;
            }

            $joinType  = in_array(strtoupper($def['join_type'] ?? 'LEFT'), ['LEFT', 'RIGHT', 'INNER', 'CROSS'], true)
                ? strtoupper($def['join_type'])
                : 'LEFT';
            $localCol  = $def['join_local_col'] ?? '';
            $remoteCol = $def['join_remote_col'] ?? '';

            if (! $localCol || ! $remoteCol) {
                continue;
            }

            $aliasStr = $alias ? " {$alias}" : '';
            $parts[]  = $joinType.' JOIN '.$quoted.$aliasStr
                .' ON '.$this->quoteIdentifier($localCol).' = '.$this->quoteIdentifier($remoteCol);
        }

        return ['sql' => implode(' ', $parts), 'primary_alias' => $primaryAlias];
    }

    /** คืนชื่อตาราง primary (ไม่มี alias) สำหรับ INSERT/UPDATE/WHERE */
    private function primaryUserTable(): string
    {
        $cfg = $this->config;
        $tables = $cfg->user_tables;

        if (! empty($tables)) {
            return $this->quoteIdentifier($tables[0]['table'] ?? $cfg->user_table);
        }

        return $this->quoteIdentifier($cfg->user_table);
    }

    // ── Advanced Mode Helpers (Scenarios I–M) ─────────────────────────────

    private function isBooleanMatrix(): bool
    {
        return ($this->config->permission_mode ?? '') === 'boolean_matrix';
    }

    private function isGroupInheritance(): bool
    {
        return ($this->config->permission_mode ?? '') === 'group_inheritance';
    }

    private function isJsonColumn(): bool
    {
        return ($this->config->permission_mode ?? '') === 'json_column';
    }

    private function isDelimitedColumn(): bool
    {
        return ($this->config->permission_mode ?? '') === 'delimited_column';
    }

    private function isBitmask(): bool
    {
        return ($this->config->permission_mode ?? '') === 'bitmask';
    }

    private function isMultiLevelHierarchy(): bool
    {
        return ($this->config->permission_mode ?? '') === 'multi_level_hierarchy';
    }

    private function hasSoftDeleteJunction(): bool
    {
        return ! empty($this->config->perm_junction_active_col);
    }

    private function hasTimeBoundJunction(): bool
    {
        return ! empty($this->config->perm_valid_from_col) || ! empty($this->config->perm_valid_to_col);
    }

    private function currentTimestampExpr(): string
    {
        return 'NOW()';
    }

    // ── Mixed Permission Mode Helpers (Feature B) ─────────────────────────

    private function isMixed(): bool
    {
        return $this->config->permission_mode === 'mixed';
    }

    /** ดึงค่าจาก column side แล้วคืนเป็น permission key พร้อม prefix "col:" */
    private function getMixedColumnPermission(UcmUser $user): ?string
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_col_value_col)) {
            return null;
        }

        try {
            $pdo = $this->getConnection();
            $colTable = $this->quoteIdentifier($cfg->perm_col_table ?: $cfg->user_table);
            $idCol = $this->quoteIdentifier($cfg->perm_col_identifier ?: $cfg->user_identifier_col);
            $valCol = $this->quoteIdentifier($cfg->perm_col_value_col);
            $identifier = $this->resolveUserIdentifier($user);

            $stmt = $pdo->prepare("SELECT {$valCol} FROM {$colTable} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $val = $stmt->fetchColumn();

            return ($val !== false && $val !== null && $val !== '') ? 'col:'.(string) $val : null;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getMixedColumnPermission: ".$e->getMessage());

            return null;
        }
    }

    /**
     * เขียน column side ของ mixed mode
     * ถ้า $rawValue = null → set column เป็น NULL
     */
    /**
     * @param  bool  $throwOnError  ถ้า true → ให้ PDOException propagate ขึ้นไป (ใช้เมื่ออยู่ใน transaction)
     */
    private function syncMixedColumnSide(UcmUser $user, ?string $rawValue, bool $throwOnError = false): bool
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_col_value_col)) {
            return true;
        }

        if ($rawValue === null) {
            Log::info("[DynamicAdapter:{$this->system->slug}] syncMixedColumnSide — ไม่มี col permission → ข้าม UPDATE");

            return true;
        }

        $identifier = $this->resolveUserIdentifier($user);

        try {
            $pdo = $this->getConnection();
            $colTable = $this->quoteIdentifier($cfg->perm_col_table ?: $cfg->user_table);
            $idCol = $this->quoteIdentifier($cfg->perm_col_identifier ?: $cfg->user_identifier_col);
            $valCol = $this->quoteIdentifier($cfg->perm_col_value_col);

            $pdo->prepare("UPDATE {$colTable} SET {$valCol} = ? WHERE {$idCol} = ?")
                ->execute([$rawValue, $identifier]);

            return true;
        } catch (PDOException $e) {
            if ($throwOnError) {
                throw $e;
            }
            Log::error("[DynamicAdapter:{$this->system->slug}] syncMixedColumnSide: ".$e->getMessage());

            return false;
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
        $count = 1 + count($compositeCols);
        $parts = explode(':', $key, $count);
        $result = [$this->config->perm_value_col => $parts[0] ?? ''];
        foreach ($compositeCols as $i => $cc) {
            $result[$cc['col']] = $parts[$i + 1] ?? '';
        }

        return $result;
    }

    /**
     * อ่าน distinct permissions จาก junction table (ใช้ร่วมกันระหว่าง junction mode และ mixed mode)
     *
     * @return array<int, array{key: string, label: string, group: string}>
     */
    private function fetchJunctionAvailablePerms(): array
    {
        $cfg = $this->config;

        if (! $cfg->perm_table || ! $cfg->perm_value_col) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->perm_table);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            if ($this->isComposite()) {
                $compositeCols = $cfg->perm_composite_cols;
                $extraQuoted = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                $allCols = array_merge([$valCol], $extraQuoted);
                $stmt = $pdo->query('SELECT DISTINCT '.implode(', ', $allCols)." FROM {$table}");
                $rows = $stmt->fetchAll();

                return array_map(function ($row) use ($cfg, $compositeCols) {
                    $key = $this->buildCompositeKey($row);
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

            return array_map(fn ($row) => [
                'key' => (string) $row[$cfg->perm_value_col],
                'label' => $cfg->perm_label_col ? ($row[$cfg->perm_label_col] ?? '') : $row[$cfg->perm_value_col],
                'group' => $cfg->perm_group_col ? ($row[$cfg->perm_group_col] ?? 'ทั่วไป') : 'ทั่วไป',
            ], $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] fetchJunctionAvailablePerms: ".$e->getMessage());

            return [];
        }
    }

    // ── Interface Implementation ───────────────────────────────────────────

    public function testConnection(): array
    {
        try {
            $pdo = $this->getConnection();
            $pdo->query('SELECT 1');

            return ['ok' => true, 'message' => 'เชื่อมต่อสำเร็จ'];
        } catch (PDOException $e) {
            Log::warning("[DynamicAdapter:{$this->system->slug}] testConnection failed: ".$e->getMessage());

            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAvailablePermissions(): array
    {
        $cfg = $this->config;

        if ($cfg->permission_mode === 'manual') {
            return $cfg->manual_permissions ?? [];
        }

        // Boolean Matrix: return each bool column name as a permission
        if ($this->isBooleanMatrix()) {
            $cols = $cfg->perm_bool_columns ?? [];

            return array_map(fn ($col) => [
                'key' => $col,
                'label' => $col,
                'group' => 'Boolean',
            ], $cols);
        }

        // Group Inheritance: distinct perm values from the junction (permissions) table
        if ($this->isGroupInheritance()) {
            return $this->fetchGroupInheritanceAvailablePerms();
        }

        // JSON Column: return static list or scan distinct values from DB
        if ($this->isJsonColumn()) {
            if (! empty($cfg->perm_json_available)) {
                return $cfg->perm_json_available;
            }

            return $this->scanJsonColumnAvailablePerms();
        }

        // Delimited Column: return static list or scan distinct values from DB
        if ($this->isDelimitedColumn()) {
            if (! empty($cfg->perm_delimited_available)) {
                return $cfg->perm_delimited_available;
            }

            return $this->scanDelimitedColumnAvailablePerms();
        }

        // Bitmask: derive available permissions from the bitmask map
        if ($this->isBitmask()) {
            $map = $cfg->perm_bitmask_map ?? [];

            return array_values(array_map(fn ($key) => [
                'key' => $key,
                'label' => $key,
                'group' => 'Bitmask',
            ], array_values($map)));
        }

        // Multi-Level Hierarchy: available "permissions" = roles that can be assigned
        if ($this->isMultiLevelHierarchy()) {
            return $this->fetchMultiLevelHierarchyRoles();
        }

        // Mixed mode: junction side + column side options
        if ($this->isMixed()) {
            $junctionPerms = filled($cfg->perm_def_table) && filled($cfg->perm_def_value_col)
                ? $this->fetchPermDefAvailablePerms()
                : $this->fetchJunctionAvailablePerms();
            $columnPerms = array_map(fn ($opt) => [
                'key' => 'col:'.($opt['key'] ?? ''),
                'label' => $opt['label'] ?? ($opt['key'] ?? ''),
                'group' => $opt['group'] ?? 'Column',
            ], (array) ($cfg->perm_col_options ?? []));

            return array_merge($junctionPerms, $columnPerms);
        }

        // มี perm_def_table → อ่าน canonical list พร้อม label/group
        if (filled($cfg->perm_def_table) && filled($cfg->perm_def_value_col)) {
            return $this->fetchPermDefAvailablePerms();
        }

        return $this->fetchJunctionAvailablePerms();
    }

    /**
     * อ่าน available permissions จาก perm_def_table (canonical list สำหรับ 2-way sync)
     * คืน label/group/remote_value จากตาราง definition ใน remote DB
     *
     * @return array<int, array{key: string, label: string, group: string, remote_value: string}>
     */
    private function fetchPermDefAvailablePerms(): array
    {
        $cfg = $this->config;

        try {
            $pdo = $this->getConnection();
            $defTable = $this->quoteIdentifier($cfg->perm_def_table);
            $valCol = $this->quoteIdentifier($cfg->perm_def_value_col);
            $pkCol = $cfg->perm_def_pk_col ?: 'id';
            $quotedPk = $this->quoteIdentifier($pkCol);

            $cols = [$quotedPk, $valCol];
            if (filled($cfg->perm_def_label_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_label_col);
            }
            if (filled($cfg->perm_def_group_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_group_col);
            }

            $where = '';
            $params = [];
            if (filled($cfg->perm_def_soft_delete_col)) {
                $softCol = $this->quoteIdentifier($cfg->perm_def_soft_delete_col);
                if (filled($cfg->perm_def_soft_delete_val)) {
                    $where = " WHERE ({$softCol} IS NULL OR {$softCol} != ?)";
                    $params[] = $cfg->perm_def_soft_delete_val;
                } else {
                    $where = " WHERE {$softCol} IS NULL";
                }
            }

            $stmt = $pdo->prepare('SELECT '.implode(', ', $cols)." FROM {$defTable}{$where}");
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            return array_map(fn ($row) => [
                'key' => (string) $row[$cfg->perm_def_value_col],
                'label' => $cfg->perm_def_label_col ? ($row[$cfg->perm_def_label_col] ?? $row[$cfg->perm_def_value_col]) : $row[$cfg->perm_def_value_col],
                'group' => $cfg->perm_def_group_col ? ($row[$cfg->perm_def_group_col] ?? 'ทั่วไป') : 'ทั่วไป',
                'remote_value' => (string) $row[$pkCol],
            ], $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] fetchPermDefAvailablePerms: ".$e->getMessage());

            return [];
        }
    }

    /**
     * Fetch available permissions for group_inheritance mode.
     * Reads distinct perm_value_col from the junction (perm_table).
     *
     * @return array<int, array{key: string, label: string, group: string}>
     */
    private function fetchGroupInheritanceAvailablePerms(): array
    {
        $cfg = $this->config;

        if (! $cfg->perm_table || ! $cfg->perm_value_col) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->perm_table);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            $cols = [$valCol];
            if ($cfg->perm_label_col) {
                $cols[] = $this->quoteIdentifier($cfg->perm_label_col);
            }
            if ($cfg->perm_group_col) {
                $cols[] = $this->quoteIdentifier($cfg->perm_group_col);
            }

            $stmt = $pdo->query('SELECT DISTINCT '.implode(', ', $cols)." FROM {$table}");
            $rows = $stmt->fetchAll();

            return array_map(fn ($row) => [
                'key' => (string) $row[$cfg->perm_value_col],
                'label' => $cfg->perm_label_col ? ($row[$cfg->perm_label_col] ?? '') : $row[$cfg->perm_value_col],
                'group' => $cfg->perm_group_col ? ($row[$cfg->perm_group_col] ?? 'ทั่วไป') : 'ทั่วไป',
            ], $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] fetchGroupInheritanceAvailablePerms: ".$e->getMessage());

            return [];
        }
    }

    public function getCurrentPermissions(UcmUser $user): ?array
    {
        $cfg = $this->config;
        $identifier = $this->resolveUserFkValue($user);

        if ($cfg->permission_mode === 'manual') {
            // Manual mode ไม่มี remote permission storage → คืน null
            // เพื่อให้ UCM ไม่แสดง "Out of Sync" badge (ไม่มีอะไรให้เปรียบเทียบ)
            return null;
        }

        // Boolean Matrix: query the user table for columns where value = 1
        if ($this->isBooleanMatrix()) {
            return $this->getBooleanMatrixPermissions($user);
        }

        // Group Inheritance: user → via_table → perm_table
        if ($this->isGroupInheritance()) {
            return $this->getGroupInheritancePermissions($user);
        }

        // JSON Column: decode JSON array from a single column on user table
        if ($this->isJsonColumn()) {
            return $this->getJsonColumnPermissions($user);
        }

        // Delimited Column: explode delimited string from a single column on user table
        if ($this->isDelimitedColumn()) {
            return $this->getDelimitedColumnPermissions($user);
        }

        // Bitmask: decode integer bit flags from a single column on user table
        if ($this->isBitmask()) {
            return $this->getBitmaskPermissions($user);
        }

        // Multi-Level Hierarchy: user's direct role assignments from membership table
        if ($this->isMultiLevelHierarchy()) {
            return $this->getMultiLevelHierarchyPermissions($user);
        }

        // Column mode: read permission value directly from the user table (no junction FK needed)
        // Use resolveUserIdentifier() not $identifier (which is FK/PK value used for junction tables)
        if ($cfg->permission_mode === 'column') {
            if (! $cfg->perm_table || ! $cfg->perm_value_col) {
                return [];
            }

            try {
                $pdo = $this->getConnection();
                $table = $this->quoteIdentifier($cfg->perm_table);
                $valCol = $this->quoteIdentifier($cfg->perm_value_col);
                $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
                $userIdentifier = $this->resolveUserIdentifier($user);
                $stmt = $pdo->prepare("SELECT {$valCol} FROM {$table} WHERE {$idCol} = ? LIMIT 1");
                $stmt->execute([$userIdentifier]);
                $val = $stmt->fetchColumn();

                return ($val !== false && $val !== null && $val !== '') ? [$val] : [];
            } catch (PDOException $e) {
                Log::error("[DynamicAdapter:{$this->system->slug}] getCurrentPermissions (column mode): ".$e->getMessage());

                return [];
            }
        }

        if (! $cfg->perm_table || ! $cfg->perm_value_col || ! $cfg->perm_user_fk_col) {
            // Mixed mode ไม่มี junction config → คืนแค่ column side
            if ($this->isMixed()) {
                $colPerm = $this->getMixedColumnPermission($user);

                return $colPerm !== null ? [$colPerm] : [];
            }

            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->perm_table);
            $fkCol = $this->quoteIdentifier($cfg->perm_user_fk_col);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            if ($this->isComposite()) {
                $compositeCols = $cfg->perm_composite_cols;
                $extraQuoted = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                $allCols = array_merge([$valCol], $extraQuoted);
                $sql = 'SELECT '.implode(', ', $allCols)." FROM {$table} WHERE {$fkCol} = ?";
                $sql .= $this->buildJunctionWhereExtras();
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$identifier]);

                $junctionPerms = array_map(fn ($row) => $this->buildCompositeKey($row), $stmt->fetchAll());
            } else {
                $sql = "SELECT {$valCol} FROM {$table} WHERE {$fkCol} = ?";
                $sql .= $this->buildJunctionWhereExtras();
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$identifier]);
                $junctionPerms = array_column($stmt->fetchAll(), $cfg->perm_value_col);
            }

            // Mixed mode: รวม junction perms + column side (deduplicate ป้องกัน col:* ซ้ำ)
            if ($this->isMixed()) {
                $colPerm = $this->getMixedColumnPermission($user);
                if ($colPerm !== null && ! in_array($colPerm, $junctionPerms, true)) {
                    $junctionPerms[] = $colPerm;
                }
            }

            return $junctionPerms;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getCurrentPermissions: ".$e->getMessage());

            return [];
        }
    }

    /**
     * Build extra WHERE clauses for soft-delete and time-bound junction modes.
     * Called when building SELECT for junction table queries.
     */
    private function buildJunctionWhereExtras(): string
    {
        $cfg = $this->config;
        $parts = [];

        // Scenario J — soft-delete active flag
        if ($this->hasSoftDeleteJunction()) {
            $activeCol = $this->quoteIdentifier($cfg->perm_junction_active_col);
            $activeVal = $cfg->perm_junction_active_val ?? '1';
            $parts[] = "{$activeCol} = ".((is_numeric($activeVal)) ? $activeVal : "'".$activeVal."'");
        }

        // Scenario L — time-bounded validity
        if ($this->hasTimeBoundJunction()) {
            if (filled($cfg->perm_valid_from_col)) {
                $fromCol = $this->quoteIdentifier($cfg->perm_valid_from_col);
                $parts[] = "({$fromCol} IS NULL OR {$fromCol} <= NOW())";
            }
            if (filled($cfg->perm_valid_to_col)) {
                $toCol = $this->quoteIdentifier($cfg->perm_valid_to_col);
                $parts[] = "({$toCol} IS NULL OR {$toCol} >= NOW())";
            }
        }

        return empty($parts) ? '' : ' AND '.implode(' AND ', $parts);
    }

    /**
     * Get permissions for boolean_matrix mode.
     * Returns column names where the user's value = 1.
     *
     * @return string[]
     */
    private function getBooleanMatrixPermissions(UcmUser $user): array
    {
        $cfg = $this->config;
        $cols = $cfg->perm_bool_columns ?? [];

        if (empty($cols)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $userTable = $this->primaryUserTable();
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            $quotedCols = implode(', ', array_map(fn ($c) => $this->quoteIdentifier($c), $cols));
            $stmt = $pdo->prepare("SELECT {$quotedCols} FROM {$userTable} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $row = $stmt->fetch();

            if ($row === false) {
                return [];
            }

            return array_values(array_filter($cols, fn ($col) => (string) ($row[$col] ?? '0') === '1'));
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getBooleanMatrixPermissions: ".$e->getMessage());

            return [];
        }
    }

    /**
     * Get permissions for group_inheritance mode.
     * Joins: user_table → via_table → perm_table to get all permissions.
     *
     * @return string[]
     */
    private function getGroupInheritancePermissions(UcmUser $user): array
    {
        $cfg = $this->config;

        if (! $cfg->perm_via_table || ! $cfg->perm_via_user_fk_col || ! $cfg->perm_via_group_fk_col) {
            return [];
        }
        if (! $cfg->perm_table || ! $cfg->perm_value_col) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $identifier = $this->resolveUserIdentifier($user);

            $viaTable = $this->quoteIdentifier($cfg->perm_via_table);
            $viaUserFk = $this->quoteIdentifier($cfg->perm_via_user_fk_col);
            $viaGroupFk = $this->quoteIdentifier($cfg->perm_via_group_fk_col);
            $permTable = $this->quoteIdentifier($cfg->perm_table);

            // The perm_user_fk_col on perm_table is the group FK (same column name as via_table group fk)
            $permGroupFk = $this->quoteIdentifier($cfg->perm_user_fk_col ?? $cfg->perm_via_group_fk_col);
            $permValCol = $this->quoteIdentifier($cfg->perm_value_col);

            $sql = "SELECT DISTINCT {$permTable}.{$permValCol}
                    FROM {$viaTable}
                    JOIN {$permTable} ON {$permTable}.{$permGroupFk} = {$viaTable}.{$viaGroupFk}
                    WHERE {$viaTable}.{$viaUserFk} = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$identifier]);

            return array_column($stmt->fetchAll(), $cfg->perm_value_col);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getGroupInheritancePermissions: ".$e->getMessage());

            return [];
        }
    }

    public function syncPermissions(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;

        if ($cfg->permission_mode === 'manual') {
            // ถ้า config ระบุ user_table → ตรวจสอบว่า user มีอยู่ในระบบปลายทางหรือไม่
            // ถ้าไม่มีให้สร้างอัตโนมัติ (permissions ใน manual mode ไม่ได้เขียนลง remote DB)
            if ($cfg->user_table && $cfg->user_identifier_col) {
                try {
                    $identStr = $this->resolveUserIdentifier($user);
                    $userTable = $this->quoteIdentifier($cfg->user_table);
                    $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
                    $pdo = $this->getConnection();
                    $chk = $pdo->prepare("SELECT 1 FROM {$userTable} WHERE {$idCol} = ? LIMIT 1");
                    $chk->execute([$identStr]);
                    if (! $chk->fetchColumn()) {
                        Log::info("[DynamicAdapter:{$this->system->slug}] Manual mode — user {$identStr} ยังไม่มีในระบบปลายทาง → กำลังสร้าง...");
                        if (! $this->createUser($user, [])) {
                            return false;
                        }
                        Log::info("[DynamicAdapter:{$this->system->slug}] Manual mode — สร้าง user {$identStr} สำเร็จ");
                    }
                } catch (PDOException $e) {
                    Log::error("[DynamicAdapter:{$this->system->slug}] Manual mode — ตรวจ/สร้าง user ล้มเหลว: ".$e->getMessage());

                    return false;
                }
            }

            return true;
        }

        // Boolean Matrix: UPDATE user row, set each bool col to 0/1
        if ($this->isBooleanMatrix()) {
            return $this->syncBooleanMatrix($user, $permissions);
        }

        // Group Inheritance: manage via_table rows (INSERT/DELETE)
        if ($this->isGroupInheritance()) {
            return $this->syncGroupInheritance($user, $permissions);
        }

        // JSON Column: encode permissions as JSON array into a single user column
        if ($this->isJsonColumn()) {
            return $this->syncJsonColumnMode($user, $permissions);
        }

        // Delimited Column: implode permissions into a delimited string in a single user column
        if ($this->isDelimitedColumn()) {
            return $this->syncDelimitedColumnMode($user, $permissions);
        }

        // Bitmask: compute integer bit flags and update a single user column
        if ($this->isBitmask()) {
            return $this->syncBitmaskMode($user, $permissions);
        }

        // Multi-Level Hierarchy: manage role memberships in user_role table
        if ($this->isMultiLevelHierarchy()) {
            return $this->syncMultiLevelHierarchy($user, $permissions);
        }

        if (! $cfg->perm_table || ! $cfg->perm_value_col) {
            return false;
        }

        // column mode — UPDATE user row โดยตรง (ไม่มี junction table)
        if ($cfg->permission_mode === 'column') {
            return $this->syncColumnMode($user, $permissions);
        }

        // mixed mode — แยก permissions เป็น junction side และ column side
        if ($this->isMixed()) {
            $colPerms = array_filter($permissions, fn ($p) => str_starts_with($p, 'col:'));
            $junctionPerms = array_values(array_filter($permissions, fn ($p) => ! str_starts_with($p, 'col:')));

            // ดึง raw value สำหรับ column side (ตัดคำนำหน้า "col:" ออก)
            $rawColValue = ! empty($colPerms) ? substr(array_values($colPerms)[0], 4) : null;

            // Sync junction side ก่อน แล้วค่อย sync column side ภายใน transaction เดียวกัน
            $permissions = $junctionPerms;
        }

        // junction mode — ต้องมี FK column
        if (! $cfg->perm_user_fk_col) {
            // Mixed mode ไม่มี junction config → sync แค่ column side
            if ($this->isMixed()) {
                return $this->syncMixedColumnSide($user, $rawColValue ?? null);
            }

            return false;
        }

        try {
            // ตรวจสอบว่า user มีอยู่ใน user_table ไหม → ถ้าไม่มีให้สร้าง
            if ($cfg->user_table && $cfg->user_identifier_col) {
                $identStr = $this->resolveUserIdentifier($user);
                $userTable = $this->quoteIdentifier($cfg->user_table);
                $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
                $pdo = $this->getConnection();
                $chk = $pdo->prepare("SELECT 1 FROM {$userTable} WHERE {$idCol} = ? LIMIT 1");
                $chk->execute([$identStr]);
                if (! $chk->fetchColumn()) {
                    Log::info("[DynamicAdapter:{$this->system->slug}] User {$identStr} ยังไม่มี → กำลังสร้าง...");
                    if (! $this->createUser($user, $permissions)) {
                        return false;
                    }
                    Log::info("[DynamicAdapter:{$this->system->slug}] สร้าง user {$identStr} สำเร็จ");

                    return true; // createUser จัดการ permissions แล้ว
                }
            }

            $identifier = $this->resolveUserFkValue($user);
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->perm_table);
            $fkCol = $this->quoteIdentifier($cfg->perm_user_fk_col);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);

            if ($this->isComposite()) {
                $compositeCols = $cfg->perm_composite_cols;
                $extraQuoted = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                $allInsertCols = array_merge([$fkCol, $valCol], $extraQuoted);
                $placeholders = implode(', ', array_fill(0, count($allInsertCols), '?'));
                $pdo->beginTransaction();
                $pdo->prepare("DELETE FROM {$table} WHERE {$fkCol} = ?")->execute([$identifier]);
                if (! empty($permissions)) {
                    $ins = $pdo->prepare('INSERT INTO '.$table.' ('.implode(', ', $allInsertCols).') VALUES ('.$placeholders.')');
                    foreach ($permissions as $permKey) {
                        $parsed = $this->parseCompositeKey($permKey);
                        $vals = [$identifier, $parsed[$cfg->perm_value_col] ?? ''];
                        foreach ($compositeCols as $cc) {
                            $vals[] = $parsed[$cc['col']] ?? '';
                        }
                        $ins->execute($vals);
                    }
                }
                // Mixed mode: sync column side ก่อน commit → rollback ได้หาก column update ล้มเหลว
                if ($this->isMixed()) {
                    $this->syncMixedColumnSide($user, $rawColValue ?? null, true);
                }

                $pdo->commit();

                return true;
            }

            $pdo->beginTransaction();

            // Scenario J — soft-delete: mark removed rows inactive instead of deleting them
            if ($this->hasSoftDeleteJunction()) {
                $activeCol = $this->quoteIdentifier($cfg->perm_junction_active_col);
                $activeVal = $cfg->perm_junction_active_val ?? '1';
                $inactiveVal = $cfg->perm_junction_inactive_val ?? '0';

                // Deactivate all existing rows for this user
                $pdo->prepare("UPDATE {$table} SET {$activeCol} = ? WHERE {$fkCol} = ?")
                    ->execute([$inactiveVal, $identifier]);

                // Fetch existing values once (ป้องกัน N+1 ต่อ permission)
                $existingStmt = $pdo->prepare("SELECT {$valCol} FROM {$table} WHERE {$fkCol} = ?");
                $existingStmt->execute([$identifier]);
                $existingValues = $existingStmt->fetchAll(PDO::FETCH_COLUMN);

                // Reactivate or insert each requested permission
                foreach ($permissions as $perm) {
                    if (in_array($perm, $existingValues, true)) {
                        $pdo->prepare("UPDATE {$table} SET {$activeCol} = ? WHERE {$fkCol} = ? AND {$valCol} = ?")
                            ->execute([$activeVal, $identifier, $perm]);
                    } else {
                        $this->junctionInsertRow($pdo, $table, $fkCol, $valCol, $identifier, $perm, $activeCol, $activeVal);
                    }
                }
            } else {
                $pdo->prepare("DELETE FROM {$table} WHERE {$fkCol} = ?")->execute([$identifier]);
                foreach ($permissions as $perm) {
                    $this->junctionInsertRow($pdo, $table, $fkCol, $valCol, $identifier, $perm);
                }
            }

            // Mixed mode: sync column side ก่อน commit → rollback ได้หาก column update ล้มเหลว
            if ($this->isMixed()) {
                $this->syncMixedColumnSide($user, $rawColValue ?? null, true);
            }

            $pdo->commit();

            return true;
        } catch (PDOException $e) {
            if ($this->pdo?->inTransaction()) {
                $this->pdo->rollBack();
            }
            Log::error("[DynamicAdapter:{$this->system->slug}] syncPermissions: ".$e->getMessage());

            return false;
        }
    }

    /**
     * INSERT a single row into the junction table, appending active-flag and insert-meta columns when configured.
     *
     * @param  string|null  $activeCol  Quoted active-flag column (soft-delete mode)
     * @param  string|null  $activeVal  Value meaning "active"
     */
    private function junctionInsertRow(
        PDO $pdo,
        string $table,
        string $fkCol,
        string $valCol,
        string $identifier,
        string $perm,
        ?string $activeCol = null,
        ?string $activeVal = null,
    ): void {
        $cfg = $this->config;
        $cols = [$fkCol, $valCol];
        $vals = [$identifier, $perm];

        // Soft-delete active flag
        if ($activeCol !== null && $activeVal !== null) {
            $cols[] = $activeCol;
            $vals[] = $activeVal;
        }

        // Scenario M — insert metadata
        $metaMap = $cfg->perm_insert_meta_cols ?? [];
        foreach ($metaMap as $metaColName => $token) {
            $cols[] = $this->quoteIdentifier($metaColName);
            $vals[] = match ($token) {
                '__now__' => date('Y-m-d H:i:s'),
                '__ucm_admin__' => auth()->user()?->username ?? 'ucm',
                default => $token,
            };
        }

        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $pdo->prepare('INSERT INTO '.$table.' ('.implode(', ', $cols).') VALUES ('.$placeholders.')')
            ->execute($vals);
    }

    public function createUser(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;
        $identifier = $this->resolveUserIdentifier($user);

        try {
            $pdo = $this->getConnection();
            $userTable = $this->primaryUserTable();

            // สร้าง column => value จาก UCM fields ที่ config ระบุ
            $data = [$cfg->user_identifier_col => $identifier];
            if (filled($cfg->user_name_col)) {
                $data[$cfg->user_name_col] = $user->name ?? '';
            }
            if (filled($cfg->user_email_col)) {
                $data[$cfg->user_email_col] = $user->email ?? '';
            }
            if (filled($cfg->user_dept_col)) {
                $data[$cfg->user_dept_col] = $this->resolveDeptValue($user);
            }
            if (filled($cfg->user_status_col) && filled($cfg->user_status_active_val)) {
                $data[$cfg->user_status_col] = $cfg->user_status_active_val;
            }
            // column mode — ใส่ค่า permission ลงใน row เดียวกัน
            if ($cfg->permission_mode === 'column' && filled($cfg->perm_value_col) && ! empty($permissions)) {
                $data[$cfg->perm_value_col] = $permissions[0];
            }
            // json_column mode — encode permissions เป็น JSON array ลงใน user row
            if ($this->isJsonColumn() && filled($cfg->perm_json_col)) {
                $data[$cfg->perm_json_col] = json_encode(array_values($permissions));
            }
            // delimited_column mode — implode permissions เป็น string ลงใน user row
            if ($this->isDelimitedColumn() && filled($cfg->perm_delimited_col)) {
                $delimiter = $cfg->perm_delimiter ?: ',';
                $data[$cfg->perm_delimited_col] = implode($delimiter, $permissions);
            }
            // bitmask mode — คำนวณ bit flags ลงใน user row
            if ($this->isBitmask() && filled($cfg->perm_bitmask_col) && ! empty($cfg->perm_bitmask_map)) {
                $flags = 0;
                foreach ($cfg->perm_bitmask_map as $bit => $permKey) {
                    if (in_array($permKey, $permissions, true)) {
                        $flags |= (int) $bit;
                    }
                }
                $data[$cfg->perm_bitmask_col] = $flags;
            }
            // mixed mode — ใส่ค่า column side permission ลงใน user row (ถ้า perm_col_table เป็นตารางเดียวกัน)
            $mixedColVal = null;
            if ($this->isMixed() && filled($cfg->perm_col_value_col)) {
                $colTable = $cfg->perm_col_table ?: $cfg->user_table;
                $primaryTable = $cfg->user_tables[0]['table'] ?? $cfg->user_table;
                if ($colTable === $primaryTable) {
                    $colPerms = array_filter($permissions, fn ($p) => str_starts_with($p, 'col:'));
                    if (! empty($colPerms)) {
                        $mixedColVal = substr(array_values($colPerms)[0], 4);
                        $data[$cfg->perm_col_value_col] = $mixedColVal;
                    }
                }
            }

            $quotedCols = implode(', ', array_map(fn ($c) => $this->quoteIdentifier($c), array_keys($data)));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            try {
                $pdo->prepare("INSERT INTO {$userTable} ({$quotedCols}) VALUES ({$placeholders})")
                    ->execute(array_values($data));
            } catch (PDOException $insertErr) {
                // Full INSERT failed (e.g. data too long for optional column) — retry with minimal fields only
                Log::warning("[DynamicAdapter:{$this->system->slug}] createUser: full INSERT failed ({$insertErr->getMessage()}), retrying with minimal fields...");
                $minData = [$cfg->user_identifier_col => $identifier];
                if (filled($cfg->user_name_col)) {
                    $minData[$cfg->user_name_col] = mb_substr($user->name ?? '', 0, 255);
                }
                if (filled($cfg->user_status_col) && filled($cfg->user_status_active_val)) {
                    $minData[$cfg->user_status_col] = $cfg->user_status_active_val;
                }
                $minCols = implode(', ', array_map(fn ($c) => $this->quoteIdentifier($c), array_keys($minData)));
                $minPlaceholders = implode(', ', array_fill(0, count($minData), '?'));
                $pdo->prepare("INSERT INTO {$userTable} ({$minCols}) VALUES ({$minPlaceholders})")
                    ->execute(array_values($minData));
                Log::info("[DynamicAdapter:{$this->system->slug}] createUser: minimal INSERT สำเร็จ (บางฟิลด์ถูกข้ามเนื่องจาก constraint)");
            }

            Log::info("[DynamicAdapter:{$this->system->slug}] createUser: สร้าง user {$identifier} สำเร็จ");

            // junction mode — sync permissions เข้า perm_table หลัง INSERT user
            if ($cfg->permission_mode !== 'column' && $cfg->perm_table && $cfg->perm_user_fk_col && ! empty($permissions)) {
                $fkVal = filled($cfg->user_pk_col) ? ($pdo->lastInsertId() ?: $identifier) : $identifier;
                $table = $this->quoteIdentifier($cfg->perm_table);
                $fkCol = $this->quoteIdentifier($cfg->perm_user_fk_col);
                $valCol = $this->quoteIdentifier($cfg->perm_value_col);

                if ($this->isComposite()) {
                    $compositeCols = $cfg->perm_composite_cols;
                    $extraQuoted = array_map(fn ($cc) => $this->quoteIdentifier($cc['col']), $compositeCols);
                    $allInsertCols = array_merge([$fkCol, $valCol], $extraQuoted);
                    $placeholders = implode(', ', array_fill(0, count($allInsertCols), '?'));
                    $ins = $pdo->prepare('INSERT INTO '.$table.' ('.implode(', ', $allInsertCols).') VALUES ('.$placeholders.')');
                    foreach ($permissions as $permKey) {
                        $parsed = $this->parseCompositeKey($permKey);
                        $vals = [$fkVal, $parsed[$cfg->perm_value_col] ?? ''];
                        foreach ($compositeCols as $cc) {
                            $vals[] = $parsed[$cc['col']] ?? '';
                        }
                        $ins->execute($vals);
                    }
                } else {
                    $ins = $pdo->prepare("INSERT INTO {$table} ({$fkCol}, {$valCol}) VALUES (?, ?)");
                    foreach ($permissions as $perm) {
                        $ins->execute([$fkVal, $perm]);
                    }
                }
            }

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] createUser failed for {$identifier}: ".$e->getMessage());

            return false;
        }
    }

    // ── JSON Column Mode ───────────────────────────────────────────────────

    private function getJsonColumnPermissions(UcmUser $user): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_json_col) || ! filled($cfg->user_identifier_col)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $jsonCol = $this->quoteIdentifier($cfg->perm_json_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            $stmt = $pdo->prepare("SELECT {$jsonCol} FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $raw = $stmt->fetchColumn();

            if (! is_string($raw) || $raw === '') {
                return [];
            }

            $decoded = json_decode($raw, true);

            return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getJsonColumnPermissions: ".$e->getMessage());

            return [];
        }
    }

    private function syncJsonColumnMode(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_json_col) || ! filled($cfg->user_identifier_col)) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $jsonCol = $this->quoteIdentifier($cfg->perm_json_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            // ตรวจว่า user มีอยู่แล้วหรือไม่
            $chk = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $chk->execute([$identifier]);

            if ($chk->fetchColumn()) {
                $pdo->prepare("UPDATE {$table} SET {$jsonCol} = ? WHERE {$idCol} = ?")
                    ->execute([json_encode(array_values($permissions)), $identifier]);
            } else {
                $this->createUser($user, $permissions);
            }

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] syncJsonColumnMode: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Scan all rows in user_table and collect distinct permission values
     * stored as JSON arrays in perm_json_col. Expensive for large tables.
     *
     * @return array<int, array{key: string, label: string, group: string}>
     */
    private function scanJsonColumnAvailablePerms(): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_json_col)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $jsonCol = $this->quoteIdentifier($cfg->perm_json_col);

            $stmt = $pdo->query("SELECT DISTINCT {$jsonCol} FROM {$table} WHERE {$jsonCol} IS NOT NULL");
            $keys = [];

            foreach ($stmt->fetchAll() as $row) {
                $decoded = json_decode($row[$cfg->perm_json_col], true);
                if (is_array($decoded)) {
                    foreach ($decoded as $v) {
                        if (is_string($v) && $v !== '') {
                            $keys[$v] = true;
                        }
                    }
                }
            }

            return array_values(array_map(fn ($k) => [
                'key' => $k,
                'label' => $k,
                'group' => 'JSON Column',
            ], array_keys($keys)));
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] scanJsonColumnAvailablePerms: ".$e->getMessage());

            return [];
        }
    }

    // ── Delimited Column Mode ──────────────────────────────────────────────

    private function getDelimitedColumnPermissions(UcmUser $user): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_delimited_col) || ! filled($cfg->user_identifier_col)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $delimCol = $this->quoteIdentifier($cfg->perm_delimited_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            $stmt = $pdo->prepare("SELECT {$delimCol} FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $raw = $stmt->fetchColumn();

            if (! is_string($raw) || $raw === '') {
                return [];
            }

            $delimiter = $cfg->perm_delimiter ?: ',';

            return array_values(array_filter(array_map('trim', explode($delimiter, $raw))));
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getDelimitedColumnPermissions: ".$e->getMessage());

            return [];
        }
    }

    private function syncDelimitedColumnMode(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_delimited_col) || ! filled($cfg->user_identifier_col)) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $delimCol = $this->quoteIdentifier($cfg->perm_delimited_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);
            $delimiter = $cfg->perm_delimiter ?: ',';

            $chk = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $chk->execute([$identifier]);

            if ($chk->fetchColumn()) {
                $pdo->prepare("UPDATE {$table} SET {$delimCol} = ? WHERE {$idCol} = ?")
                    ->execute([implode($delimiter, $permissions), $identifier]);
            } else {
                $this->createUser($user, $permissions);
            }

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] syncDelimitedColumnMode: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Scan all rows and collect distinct permission values from delimited strings.
     *
     * @return array<int, array{key: string, label: string, group: string}>
     */
    private function scanDelimitedColumnAvailablePerms(): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_delimited_col)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $delimCol = $this->quoteIdentifier($cfg->perm_delimited_col);
            $delimiter = $cfg->perm_delimiter ?: ',';

            $stmt = $pdo->query("SELECT {$delimCol} FROM {$table} WHERE {$delimCol} IS NOT NULL AND {$delimCol} != ''");
            $keys = [];

            foreach ($stmt->fetchAll() as $row) {
                $parts = array_filter(array_map('trim', explode($delimiter, $row[$cfg->perm_delimited_col])));
                foreach ($parts as $v) {
                    $keys[$v] = true;
                }
            }

            return array_values(array_map(fn ($k) => [
                'key' => $k,
                'label' => $k,
                'group' => 'Delimited Column',
            ], array_keys($keys)));
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] scanDelimitedColumnAvailablePerms: ".$e->getMessage());

            return [];
        }
    }

    // ── Bitmask Mode ───────────────────────────────────────────────────────

    private function getBitmaskPermissions(UcmUser $user): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_bitmask_col) || empty($cfg->perm_bitmask_map)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $bitmaskCol = $this->quoteIdentifier($cfg->perm_bitmask_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            $stmt = $pdo->prepare("SELECT {$bitmaskCol} FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $flags = (int) $stmt->fetchColumn();

            $result = [];
            foreach ($cfg->perm_bitmask_map as $bit => $permKey) {
                if ($flags & (int) $bit) {
                    $result[] = (string) $permKey;
                }
            }

            return $result;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getBitmaskPermissions: ".$e->getMessage());

            return [];
        }
    }

    private function syncBitmaskMode(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_bitmask_col) || empty($cfg->perm_bitmask_map)) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->user_table);
            $bitmaskCol = $this->quoteIdentifier($cfg->perm_bitmask_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            $flags = 0;
            foreach ($cfg->perm_bitmask_map as $bit => $permKey) {
                if (in_array((string) $permKey, $permissions, true)) {
                    $flags |= (int) $bit;
                }
            }

            $chk = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $chk->execute([$identifier]);

            if ($chk->fetchColumn()) {
                $pdo->prepare("UPDATE {$table} SET {$bitmaskCol} = ? WHERE {$idCol} = ?")
                    ->execute([$flags, $identifier]);
            } else {
                $this->createUser($user, $permissions);
            }

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] syncBitmaskMode: ".$e->getMessage());

            return false;
        }
    }

    // ── Multi-Level Hierarchy Mode ─────────────────────────────────────────

    /**
     * Fetch all roles from the roles table as available "permissions" to assign.
     * In this mode UCM admins assign roles (not leaf permissions) to users.
     *
     * @return array<int, array{key: string, label: string, group: string}>
     */
    private function fetchMultiLevelHierarchyRoles(): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_hier_role_table) || ! filled($cfg->perm_hier_role_pk_col)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $roleTable = $this->quoteIdentifier($cfg->perm_hier_role_table);
            $pkCol = $this->quoteIdentifier($cfg->perm_hier_role_pk_col);
            $cols = [$pkCol];

            if (filled($cfg->perm_hier_role_name_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_hier_role_name_col);
            }

            $stmt = $pdo->query('SELECT '.implode(', ', $cols)." FROM {$roleTable}");
            $rows = $stmt->fetchAll();

            return array_map(fn ($row) => [
                'key' => (string) $row[$cfg->perm_hier_role_pk_col],
                'label' => $cfg->perm_hier_role_name_col
                    ? ($row[$cfg->perm_hier_role_name_col] ?? $row[$cfg->perm_hier_role_pk_col])
                    : $row[$cfg->perm_hier_role_pk_col],
                'group' => 'Role',
            ], $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] fetchMultiLevelHierarchyRoles: ".$e->getMessage());

            return [];
        }
    }

    /**
     * Get user's direct role assignments from the membership table.
     * Returns role PKs (same values shown as keys in getAvailablePermissions).
     *
     * @return array<int, string>
     */
    private function getMultiLevelHierarchyPermissions(UcmUser $user): array
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_hier_user_role_table)
            || ! filled($cfg->perm_hier_user_fk_col)
            || ! filled($cfg->perm_hier_role_fk_col)) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $memberTable = $this->quoteIdentifier($cfg->perm_hier_user_role_table);
            $userFk = $this->quoteIdentifier($cfg->perm_hier_user_fk_col);
            $roleFk = $this->quoteIdentifier($cfg->perm_hier_role_fk_col);
            $identifier = $this->resolveUserIdentifier($user);

            $stmt = $pdo->prepare("SELECT {$roleFk} FROM {$memberTable} WHERE {$userFk} = ?");
            $stmt->execute([$identifier]);

            return array_column($stmt->fetchAll(), $cfg->perm_hier_role_fk_col);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getMultiLevelHierarchyPermissions: ".$e->getMessage());

            return [];
        }
    }

    /**
     * Sync role memberships: INSERT/DELETE rows in the membership table.
     * Also supports reading effective permissions recursively through role hierarchy.
     */
    private function syncMultiLevelHierarchy(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;

        if (! filled($cfg->perm_hier_user_role_table)
            || ! filled($cfg->perm_hier_user_fk_col)
            || ! filled($cfg->perm_hier_role_fk_col)) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $identifier = $this->resolveUserIdentifier($user);
            $memberTable = $this->quoteIdentifier($cfg->perm_hier_user_role_table);
            $userFk = $this->quoteIdentifier($cfg->perm_hier_user_fk_col);
            $roleFk = $this->quoteIdentifier($cfg->perm_hier_role_fk_col);

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT {$roleFk} FROM {$memberTable} WHERE {$userFk} = ?");
            $stmt->execute([$identifier]);
            $currentRoles = array_column($stmt->fetchAll(), $cfg->perm_hier_role_fk_col);

            $toAdd = array_diff($permissions, $currentRoles);
            $toRemove = array_diff($currentRoles, $permissions);

            foreach ($toRemove as $role) {
                $pdo->prepare("DELETE FROM {$memberTable} WHERE {$userFk} = ? AND {$roleFk} = ?")
                    ->execute([$identifier, $role]);
            }

            foreach ($toAdd as $role) {
                $pdo->prepare("INSERT INTO {$memberTable} ({$userFk}, {$roleFk}) VALUES (?, ?)")
                    ->execute([$identifier, $role]);
            }

            $pdo->commit();

            return true;
        } catch (PDOException $e) {
            if ($this->pdo?->inTransaction()) {
                $this->pdo->rollBack();
            }
            Log::error("[DynamicAdapter:{$this->system->slug}] syncMultiLevelHierarchy: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Traverse role hierarchy recursively to get all effective role IDs
     * including ancestors. Uses WITH RECURSIVE CTE for MySQL 8+, PostgreSQL,
     * SQL Server. Falls back to iterative loop for older MySQL.
     *
     * @param  array<int, string>  $directRoleIds
     * @return array<int, string>
     */
    private function resolveRoleChainRecursive(array $directRoleIds): array
    {
        $cfg = $this->config;

        if (empty($directRoleIds)
            || ! filled($cfg->perm_hier_role_table)
            || ! filled($cfg->perm_hier_role_pk_col)
            || ! filled($cfg->perm_hier_role_parent_col)) {
            return $directRoleIds;
        }

        try {
            $pdo = $this->getConnection();
            $roleTable = $this->quoteIdentifier($cfg->perm_hier_role_table);
            $pkCol = $this->quoteIdentifier($cfg->perm_hier_role_pk_col);
            $parentCol = $this->quoteIdentifier($cfg->perm_hier_role_parent_col);

            // ลอง WITH RECURSIVE ก่อน (MySQL 8+, PostgreSQL, SQL Server)
            try {
                $placeholders = implode(',', array_fill(0, count($directRoleIds), '?'));
                $sql = "WITH RECURSIVE role_chain AS (
                    SELECT {$pkCol}, {$parentCol} FROM {$roleTable} WHERE {$pkCol} IN ({$placeholders})
                    UNION ALL
                    SELECT r.{$pkCol}, r.{$parentCol} FROM {$roleTable} r
                    INNER JOIN role_chain rc ON r.{$pkCol} = rc.{$parentCol}
                )
                SELECT DISTINCT {$pkCol} FROM role_chain";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($directRoleIds);

                return array_column($stmt->fetchAll(), $cfg->perm_hier_role_pk_col);
            } catch (PDOException) {
                // Fallback: iterative traversal สูงสุด 10 levels สำหรับ MySQL 5.7
                $allRoles = $directRoleIds;
                $toProcess = $directRoleIds;
                $depth = 0;

                while (! empty($toProcess) && $depth < 10) {
                    $placeholders = implode(',', array_fill(0, count($toProcess), '?'));
                    $stmt = $pdo->prepare("SELECT {$pkCol} FROM {$roleTable} WHERE {$parentCol} IN ({$placeholders})");
                    $stmt->execute($toProcess);
                    $parents = array_column($stmt->fetchAll(), $cfg->perm_hier_role_pk_col);
                    $toProcess = array_diff($parents, $allRoles);
                    $allRoles = array_merge($allRoles, $toProcess);
                    $depth++;
                }

                return array_values(array_unique($allRoles));
            }
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] resolveRoleChainRecursive: ".$e->getMessage());

            return $directRoleIds;
        }
    }

    private function syncColumnMode(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;
        $identifier = $this->resolveUserIdentifier($user);
        $val = $permissions[0] ?? null;

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg->perm_table);
            $valCol = $this->quoteIdentifier($cfg->perm_value_col);
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);

            // ตรวจสอบว่า user มีอยู่ไหม
            $chk = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$idCol} = ? LIMIT 1");
            $chk->execute([$identifier]);
            if (! $chk->fetchColumn()) {
                if ($val === null) {
                    // ไม่มี permission และ user ยังไม่มี → ไม่มีอะไรต้องทำ
                    return true;
                }
                Log::info("[DynamicAdapter:{$this->system->slug}] Column mode — User {$identifier} ยังไม่มี → กำลังสร้าง...");
                if (! $this->createUser($user, $permissions)) {
                    return false;
                }
                Log::info("[DynamicAdapter:{$this->system->slug}] สร้าง user {$identifier} สำเร็จ");

                return true;
            }

            if ($val === null) {
                // ไม่มี permission ที่เลือก → ข้าม UPDATE เพื่อป้องกัน NOT NULL violation
                Log::info("[DynamicAdapter:{$this->system->slug}] Column mode — User {$identifier} ไม่มี permission ที่เลือก → ข้าม UPDATE");

                return true;
            }

            // User มีอยู่แล้ว → UPDATE column
            $pdo->prepare("UPDATE {$table} SET {$valCol} = ? WHERE {$idCol} = ?")
                ->execute([$val, $identifier]);

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] syncColumnMode: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Sync boolean_matrix mode: UPDATE user row setting each bool column to 0 or 1.
     */
    private function syncBooleanMatrix(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;
        $cols = $cfg->perm_bool_columns ?? [];

        if (empty($cols)) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $userTable = $this->primaryUserTable();
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $identifier = $this->resolveUserIdentifier($user);

            $setParts = array_map(fn ($col) => $this->quoteIdentifier($col).' = ?', $cols);
            $vals = array_map(fn ($col) => in_array($col, $permissions, true) ? 1 : 0, $cols);
            $vals[] = $identifier;

            $pdo->prepare('UPDATE '.$userTable.' SET '.implode(', ', $setParts)." WHERE {$idCol} = ?")
                ->execute($vals);

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] syncBooleanMatrix: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Sync group_inheritance mode: manage via_table rows (INSERT/DELETE).
     * Permissions are group/role codes stored in perm_via_group_fk_col of the via table.
     */
    private function syncGroupInheritance(UcmUser $user, array $permissions): bool
    {
        $cfg = $this->config;

        if (! $cfg->perm_via_table || ! $cfg->perm_via_user_fk_col || ! $cfg->perm_via_group_fk_col) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $identifier = $this->resolveUserIdentifier($user);

            $viaTable = $this->quoteIdentifier($cfg->perm_via_table);
            $viaUserFk = $this->quoteIdentifier($cfg->perm_via_user_fk_col);
            $viaGroupFk = $this->quoteIdentifier($cfg->perm_via_group_fk_col);

            $pdo->beginTransaction();

            // Get current group memberships for this user
            $stmt = $pdo->prepare("SELECT {$viaGroupFk} FROM {$viaTable} WHERE {$viaUserFk} = ?");
            $stmt->execute([$identifier]);
            $currentGroups = array_column($stmt->fetchAll(), $cfg->perm_via_group_fk_col);

            $toAdd = array_diff($permissions, $currentGroups);
            $toRemove = array_diff($currentGroups, $permissions);

            foreach ($toRemove as $group) {
                $pdo->prepare("DELETE FROM {$viaTable} WHERE {$viaUserFk} = ? AND {$viaGroupFk} = ?")
                    ->execute([$identifier, $group]);
            }

            foreach ($toAdd as $group) {
                $pdo->prepare("INSERT INTO {$viaTable} ({$viaUserFk}, {$viaGroupFk}) VALUES (?, ?)")
                    ->execute([$identifier, $group]);
            }

            $pdo->commit();

            return true;
        } catch (PDOException $e) {
            if ($this->pdo?->inTransaction()) {
                $this->pdo->rollBack();
            }
            Log::error("[DynamicAdapter:{$this->system->slug}] syncGroupInheritance: ".$e->getMessage());

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
            $pdo = $this->getConnection();
            ['sql' => $joinSql] = $this->buildUserJoinSql();

            $ucmUsers = UcmUser::select('username', 'employee_number')->get();
            $ucmUsernames = $ucmUsers->pluck('username')->toArray();
            $ucmEmployees = $ucmUsers->pluck('employee_number')->filter()->toArray();

            $stmt = $pdo->query("SELECT * FROM {$joinSql}");
            $rows = $stmt->fetchAll();

            return array_map(function ($row) use ($cfg, $ucmUsernames, $ucmEmployees) {
                $identifier = $row[$cfg->user_identifier_col] ?? '';
                $inUcm = in_array($identifier, $ucmUsernames, true)
                    || in_array($identifier, $ucmEmployees, true);

                $statusVal = $cfg->user_status_col ? ($row[$cfg->user_status_col] ?? null) : null;
                $isActive = $cfg->user_status_active_val !== null
                    ? ((string) $statusVal === (string) $cfg->user_status_active_val)
                    : true;

                return [
                    'username' => $identifier,
                    'name' => $cfg->user_name_col ? ($row[$cfg->user_name_col] ?? '') : $identifier,
                    'email' => $cfg->user_email_col ? ($row[$cfg->user_email_col] ?? '') : '',
                    'department' => $cfg->user_dept_col ? ($row[$cfg->user_dept_col] ?? '') : '',
                    'status' => $isActive,
                    'in_ucm' => $inUcm,
                ];
            }, $rows);
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getSystemUsers: ".$e->getMessage());

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
        $cfg = $this->config;
        $created = [];

        try {
            $this->system->loadMissing('permissions');
            $existingKeys = $this->system->permissions->pluck('key')->all();

            $rows = $this->fetchPermissionRows();

            // column mode เก็บสิทธิ์เดียวต่อ user → ต้องให้ทุก permission อยู่ใน
            // group เดียวกัน เพื่อให้ UI แสดงเป็น radio button ชุดเดียว
            $forceGroup = ($cfg->permission_mode === 'column') ? 'บทบาท' : null;

            foreach ($rows as $row) {
                $key = trim((string) ($row['key'] ?? ''));
                $remoteValue = isset($row['remote_value']) && $row['remote_value'] !== '' ? $row['remote_value'] : null;
                $group = $forceGroup ?? ($row['group'] ?? null);

                if ($key === '') {
                    continue;
                }

                if (in_array($key, $existingKeys, true)) {
                    $updates = [];
                    // อัปเดต remote_value ของ record เดิมที่ยังว่างอยู่ (backfill)
                    if ($remoteValue !== null) {
                        $updates['remote_value'] = $remoteValue;
                    }
                    // column mode: normalize group ของ permission เดิมให้ตรงกันด้วย
                    if ($forceGroup !== null) {
                        $updates['group'] = $forceGroup;
                    }
                    if (! empty($updates)) {
                        SystemPermission::where('system_id', $this->system->id)
                            ->where('key', $key)
                            ->update($updates);
                    }

                    continue;
                }

                SystemPermission::firstOrCreate(
                    ['system_id' => $this->system->id, 'key' => $key],
                    [
                        'label' => $row['label'] ?? $key,
                        'group' => $group,
                        'remote_value' => $remoteValue,
                    ]
                );

                $existingKeys[] = $key;
                $created[] = $key;

                Log::info("[DynamicAdapter:{$this->system->slug}] discoverPermissions: สร้าง '{$key}'");
            }

            // Mixed mode: สร้าง col:* permissions จาก perm_col_options ด้วย
            if ($this->isMixed() && ! empty($cfg->perm_col_options)) {
                foreach ((array) $cfg->perm_col_options as $opt) {
                    $key = 'col:'.($opt['key'] ?? '');
                    if ($key === 'col:') {
                        continue;
                    }

                    if (in_array($key, $existingKeys, true)) {
                        continue;
                    }

                    SystemPermission::updateOrCreate(
                        ['system_id' => $this->system->id, 'key' => $key],
                        [
                            'label'        => $opt['label'] ?? $key,
                            'group'        => $opt['group'] ?? 'Column',
                            'remote_value' => null,
                            'is_exclusive' => true,   // col:* เลือกได้ทีละ 1 (radio button)
                        ]
                    );

                    $existingKeys[] = $key;
                    $created[] = $key;

                    Log::info("[DynamicAdapter:{$this->system->slug}] discoverPermissions: สร้าง '{$key}' (col side)");
                }
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
                'key' => $p['key'] ?? '',
                'label' => $p['label'] ?? ($p['key'] ?? ''),
                'group' => $p['group'] ?? null,
                'remote_value' => null,
            ], (array) ($cfg->manual_permissions ?? []));
        }

        $pdo = $this->getConnection();

        // มี perm_def_table → อ่าน canonical list (2-way sync)
        if (filled($cfg->perm_def_table) && filled($cfg->perm_def_value_col)) {
            $defTable = $this->quoteIdentifier($cfg->perm_def_table);
            $valCol = $this->quoteIdentifier($cfg->perm_def_value_col);
            $pkCol = $cfg->perm_def_pk_col ?: 'id';
            $quotedPk = $this->quoteIdentifier($pkCol);

            // เลือก PK เสมอเพื่อใช้เป็น remote_value
            $cols = [$quotedPk, $valCol];
            if (filled($cfg->perm_def_label_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_label_col);
            }
            if (filled($cfg->perm_def_group_col)) {
                $cols[] = $this->quoteIdentifier($cfg->perm_def_group_col);
            }

            // กรอง soft-deleted rows ออก
            // - มี softVal (เช่น is_deleted=1): WHERE softCol IS NULL OR softCol != softVal
            // - ไม่มี softVal (เช่น deleted_at IS NULL):  WHERE softCol IS NULL
            $where = '';
            $params = [];
            if (filled($cfg->perm_def_soft_delete_col)) {
                $softCol = $this->quoteIdentifier($cfg->perm_def_soft_delete_col);
                if (filled($cfg->perm_def_soft_delete_val)) {
                    $where = " WHERE ({$softCol} IS NULL OR {$softCol} != ?)";
                    $params[] = $cfg->perm_def_soft_delete_val;
                } else {
                    // deleted_at style — active rows มี NULL
                    $where = " WHERE {$softCol} IS NULL";
                }
            }

            $stmt = $pdo->prepare('SELECT '.implode(', ', $cols)." FROM {$defTable}{$where}");
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            // Composite mode: cross-join role_definitions × distinct values ของแต่ละ composite col
            if ($this->isComposite() && ! empty($cfg->perm_composite_cols) && filled($cfg->perm_table)) {
                $junctionTable = $this->quoteIdentifier($cfg->perm_table);
                $siteValueSets = [];
                foreach ($cfg->perm_composite_cols as $cc) {
                    $ccQuoted = $this->quoteIdentifier($cc['col']);
                    $s = $pdo->query("SELECT DISTINCT {$ccQuoted} FROM {$junctionTable} WHERE {$ccQuoted} IS NOT NULL ORDER BY {$ccQuoted}");
                    $siteValueSets[$cc['col']] = $s->fetchAll(PDO::FETCH_COLUMN);
                }

                $result = [];
                foreach ($rows as $row) {
                    $roleKey = (string) $row[$cfg->perm_def_value_col];
                    $roleLabel = $cfg->perm_def_label_col ? ($row[$cfg->perm_def_label_col] ?? $roleKey) : $roleKey;
                    $roleGroup = $cfg->perm_def_group_col ? ($row[$cfg->perm_def_group_col] ?? null) : null;
                    $remotePk = (string) $row[$pkCol];

                    // สร้าง composite keys: role_key:site_val (รองรับ 1 composite col)
                    $firstCc = $cfg->perm_composite_cols[0];
                    $siteValues = $siteValueSets[$firstCc['col']] ?? [];

                    if (empty($siteValues)) {
                        // ไม่มีข้อมูล site ใน junction ให้ fallback เป็น plain key
                        $result[] = ['key' => $roleKey, 'label' => $roleLabel, 'group' => $roleGroup, 'remote_value' => $remotePk];
                    } else {
                        foreach ($siteValues as $siteVal) {
                            $result[] = [
                                'key' => $roleKey.':'.$siteVal,
                                'label' => $roleLabel.' · '.$siteVal,
                                'group' => $roleGroup,
                                'remote_value' => $remotePk,
                            ];
                        }
                    }
                }

                return $result;
            }

            return array_map(fn ($row) => [
                'key' => (string) $row[$cfg->perm_def_value_col],
                'label' => $cfg->perm_def_label_col ? ($row[$cfg->perm_def_label_col] ?? '') : $row[$cfg->perm_def_value_col],
                'group' => $cfg->perm_def_group_col ? ($row[$cfg->perm_def_group_col] ?? null) : null,
                'remote_value' => (string) $row[$pkCol],
            ], $rows);
        }

        // ไม่มี perm_def_table → อ่าน distinct values จาก perm_table (ไม่มี PK ให้ track)
        return array_map(fn ($p) => [
            'key' => $p['key'],
            'label' => $p['label'] ?? $p['key'],
            'group' => $p['group'] ?? null,
            'remote_value' => null,
        ], $this->getAvailablePermissions());
    }

    public function getPermissionDeleteMode(): PermissionDeleteMode
    {
        return PermissionDeleteMode::tryFrom($this->config->perm_delete_mode ?? '')
            ?? PermissionDeleteMode::DetachOnly;
    }

    public function getAccountStatus(UcmUser $user): ?bool
    {
        $cfg = $this->config;

        if (! filled($cfg->user_status_col)) {
            return null;
        }

        try {
            $pdo = $this->getConnection();
            $userTable = $this->primaryUserTable();
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $statusCol = $this->quoteIdentifier($cfg->user_status_col);
            $identifier = $this->resolveUserIdentifier($user);

            $stmt = $pdo->prepare("SELECT {$statusCol} FROM {$userTable} WHERE {$idCol} = ? LIMIT 1");
            $stmt->execute([$identifier]);
            $row = $stmt->fetch();

            if ($row === false) {
                return null;
            }

            $val = (string) $row[$cfg->user_status_col];

            return $cfg->user_status_active_val !== null
                ? $val === (string) $cfg->user_status_active_val
                : true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getAccountStatus: ".$e->getMessage());

            return null;
        }
    }

    public function setAccountStatus(UcmUser $user, bool $active): bool
    {
        $cfg = $this->config;

        if (! filled($cfg->user_status_col)) {
            return false;
        }

        $targetVal = $active
            ? ($cfg->user_status_active_val ?? '1')
            : ($cfg->user_status_inactive_val ?? '0');

        try {
            $pdo = $this->getConnection();
            $userTable = $this->primaryUserTable();
            $idCol = $this->quoteIdentifier($cfg->user_identifier_col);
            $statusCol = $this->quoteIdentifier($cfg->user_status_col);
            $identifier = $this->resolveUserIdentifier($user);

            $pdo->prepare("UPDATE {$userTable} SET {$statusCol} = ? WHERE {$idCol} = ?")
                ->execute([$targetVal, $identifier]);

            Log::info("[DynamicAdapter:{$this->system->slug}] setAccountStatus: {$identifier} → ".($active ? 'active' : 'inactive'));

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] setAccountStatus: ".$e->getMessage());

            return false;
        }
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
            $pdo = $this->getConnection();
            $defTable = $this->quoteIdentifier($cfg->perm_def_table);
            $valCol = $this->quoteIdentifier($cfg->perm_def_value_col);
            $pkCol = $cfg->perm_def_pk_col ?: 'id';
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
     * อัปเดต label / group ของ permission definition ใน remote DB
     *
     * เรียกเมื่อ admin แก้ไข permission ใน UCM และ 2-way เปิดอยู่
     * อัปเดตเฉพาะคอลัมน์ที่ตั้งค่าไว้ใน perm_def_label_col / perm_def_group_col
     */
    public function updatePermission(string $remoteValue, string $label, string $group): bool
    {
        $cfg = $this->config;

        if (! $cfg->perm_def_table) {
            return true;
        }

        $pkCol = $cfg->perm_def_pk_col ?: 'id';
        $defTable = $this->quoteIdentifier($cfg->perm_def_table);
        $quotedPk = $this->quoteIdentifier($pkCol);

        $setClauses = [];
        $bindings = [];

        if ($cfg->perm_def_label_col && ! blank($label)) {
            $setClauses[] = $this->quoteIdentifier($cfg->perm_def_label_col).' = ?';
            $bindings[] = $label;
        }

        if ($cfg->perm_def_group_col && ! blank($group)) {
            $setClauses[] = $this->quoteIdentifier($cfg->perm_def_group_col).' = ?';
            $bindings[] = $group;
        }

        if (empty($setClauses)) {
            return true;
        }

        $bindings[] = $remoteValue;

        try {
            $pdo = $this->getConnection();
            $pdo->prepare(
                'UPDATE '.$defTable.' SET '.implode(', ', $setClauses).' WHERE '.$quotedPk.' = ?'
            )->execute($bindings);

            Log::info("[DynamicAdapter:{$this->system->slug}] updatePermission: อัปเดต '{$remoteValue}' สำเร็จ");

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] updatePermission: ".$e->getMessage());

            return false;
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
        $cfg = $this->config;
        $mode = $this->getPermissionDeleteMode();

        if ($mode === PermissionDeleteMode::DetachOnly || ! $cfg->perm_def_table) {
            return true;
        }

        $pkCol = $cfg->perm_def_pk_col ?: 'id';
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
                // ถ้าไม่ได้ตั้งค่าไว้ ให้ใช้ datetime ปัจจุบัน (รองรับ deleted_at column)
                $softVal = $cfg->perm_def_soft_delete_val ?? date('Y-m-d H:i:s');

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

    // ── Managed Group CRUD (Master Tables) ─────────────────────────────────

    public function getManagedGroups(): array
    {
        $tables = $this->config->master_tables ?? [];

        return array_column($tables, 'label');
    }

    public function getGroupDeleteMode(string $group): string
    {
        $cfg = $this->getMasterTableConfig($group);

        return $cfg['delete_mode'] ?? 'hard';
    }

    public function getGroupSchema(string $group): array
    {
        $cfg = $this->getMasterTableConfig($group);

        if (! $cfg) {
            return [];
        }

        $schema = [];
        foreach ($cfg['extra_cols'] ?? [] as $col) {
            $schema[$col['col']] = [
                'label' => $col['label'] ?? $col['col'],
                'type' => $col['type'] ?? 'text',
                'required' => (bool) ($col['required'] ?? false),
            ];
        }

        return $schema;
    }

    public function getGroupRecords(string $group): array
    {
        $cfg = $this->getMasterTableConfig($group);

        if (! $cfg) {
            return [];
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg['table']);
            $pk = $this->quoteIdentifier($cfg['pk_col']);
            $labelCol = $this->quoteIdentifier($cfg['label_col']);

            $extraCols = '';
            foreach ($cfg['extra_cols'] ?? [] as $col) {
                $extraCols .= ', '.$this->quoteIdentifier($col['col']);
            }

            $where = '';
            if (($cfg['delete_mode'] ?? 'hard') === 'soft' && ! empty($cfg['soft_delete_col'])) {
                $softCol = $this->quoteIdentifier($cfg['soft_delete_col']);
                $softVal = $cfg['soft_delete_val'] ?? '1';
                $where = " WHERE {$softCol} != ".$pdo->quote((string) $softVal);
            }

            $sql = "SELECT {$pk} AS id, {$labelCol} AS name{$extraCols} FROM {$table}{$where}";
            $stmt = $pdo->query($sql);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] getGroupRecords({$group}): ".$e->getMessage());

            return [];
        }
    }

    public function addGroupRecord(string $group, string $name, array $extra = []): array|false
    {
        $cfg = $this->getMasterTableConfig($group);

        if (! $cfg) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg['table']);
            $labelCol = $this->quoteIdentifier($cfg['label_col']);

            $cols = [$labelCol];
            $values = [$name];

            foreach ($cfg['extra_cols'] ?? [] as $col) {
                if (array_key_exists($col['col'], $extra)) {
                    $cols[] = $this->quoteIdentifier($col['col']);
                    $values[] = $extra[$col['col']];
                }
            }

            $placeholders = implode(', ', array_fill(0, count($cols), '?'));
            $colList = implode(', ', $cols);

            $pdo->prepare("INSERT INTO {$table} ({$colList}) VALUES ({$placeholders})")
                ->execute($values);

            $newId = (int) $pdo->lastInsertId();

            Log::info("[DynamicAdapter:{$this->system->slug}] addGroupRecord({$group}): id={$newId}");

            $records = $this->getGroupRecords($group);
            foreach ($records as $record) {
                if ((int) $record['id'] === $newId) {
                    return $record;
                }
            }

            return ['id' => $newId, 'name' => $name];
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] addGroupRecord({$group}): ".$e->getMessage());

            return false;
        }
    }

    public function updateGroupRecord(string $group, int $id, string $name, array $extra = []): bool
    {
        $cfg = $this->getMasterTableConfig($group);

        if (! $cfg) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg['table']);
            $pk = $this->quoteIdentifier($cfg['pk_col']);
            $labelCol = $this->quoteIdentifier($cfg['label_col']);

            $setParts = ["{$labelCol} = ?"];
            $bindings = [$name];

            foreach ($cfg['extra_cols'] ?? [] as $col) {
                if (array_key_exists($col['col'], $extra)) {
                    $setParts[] = $this->quoteIdentifier($col['col']).' = ?';
                    $bindings[] = $extra[$col['col']];
                }
            }

            $bindings[] = $id;
            $setClause = implode(', ', $setParts);

            $pdo->prepare("UPDATE {$table} SET {$setClause} WHERE {$pk} = ?")
                ->execute($bindings);

            Log::info("[DynamicAdapter:{$this->system->slug}] updateGroupRecord({$group}): id={$id}");

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] updateGroupRecord({$group}, {$id}): ".$e->getMessage());

            return false;
        }
    }

    public function deleteGroupRecord(string $group, int $id): bool
    {
        $cfg = $this->getMasterTableConfig($group);

        if (! $cfg) {
            return false;
        }

        try {
            $pdo = $this->getConnection();
            $table = $this->quoteIdentifier($cfg['table']);
            $pk = $this->quoteIdentifier($cfg['pk_col']);

            if (($cfg['delete_mode'] ?? 'hard') === 'soft') {
                $softCol = $cfg['soft_delete_col'] ?? null;
                $softVal = $cfg['soft_delete_val'] ?? '1';

                if (! $softCol) {
                    return false;
                }

                $quotedSoftCol = $this->quoteIdentifier($softCol);
                $pdo->prepare("UPDATE {$table} SET {$quotedSoftCol} = ? WHERE {$pk} = ?")
                    ->execute([$softVal, $id]);

                Log::info("[DynamicAdapter:{$this->system->slug}] deleteGroupRecord({$group}, soft): id={$id}");
            } else {
                $pdo->prepare("DELETE FROM {$table} WHERE {$pk} = ?")
                    ->execute([$id]);

                Log::info("[DynamicAdapter:{$this->system->slug}] deleteGroupRecord({$group}, hard): id={$id}");
            }

            return true;
        } catch (PDOException $e) {
            Log::error("[DynamicAdapter:{$this->system->slug}] deleteGroupRecord({$group}, {$id}): ".$e->getMessage());

            return false;
        }
    }

    /**
     * ค้นหา master table config โดย label
     */
    private function getMasterTableConfig(string $group): ?array
    {
        foreach ($this->config->master_tables ?? [] as $entry) {
            if (($entry['label'] ?? '') === $group) {
                return $entry;
            }
        }

        return null;
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
            fn ($part) => $quote.str_replace($quote, '', $part).$quote,
            explode('.', $name)
        ));
    }
}
