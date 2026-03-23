<?php

namespace App\Services\Connector;

use PDO;

class SchemaIntrospector
{
    private PDO $pdo;

    public function __construct(
        private readonly string $driver,
        private readonly array $config
    ) {
        $this->pdo = $this->buildPdo();
    }

    public function introspect(): array
    {
        $schema = [];
        foreach ($this->getTables() as $table) {
            $schema[$table] = [
                'columns'   => $this->getColumns($table),
                'fks'       => $this->getForeignKeys($table),
                'row_count' => $this->getRowCount($table),
                'sample'    => $this->getSample($table),
            ];
        }

        return $schema;
    }

    // ── Tables ──────────────────────────────────────────────────────────────

    private function getTables(): array
    {
        return match ($this->driver) {
            'pgsql'  => array_column($this->pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public'")->fetchAll(), 'tablename'),
            'sqlsrv' => array_column($this->pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE'")->fetchAll(), 'TABLE_NAME'),
            default  => array_column($this->pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM), 0),
        };
    }

    // ── Columns ─────────────────────────────────────────────────────────────

    private function getColumns(string $table): array
    {
        return match ($this->driver) {
            'pgsql'  => $this->getPgsqlColumns($table),
            'sqlsrv' => $this->getSqlsrvColumns($table),
            default  => $this->getMysqlColumns($table),
        };
    }

    private function getMysqlColumns(string $table): array
    {
        $rows = $this->pdo->query('DESCRIBE ' . $this->qi($table))->fetchAll();

        return array_map(fn ($r) => [
            'name'     => $r['Field'],
            'type'     => $r['Type'],
            'nullable' => $r['Null'] === 'YES',
            'pk'       => $r['Key'] === 'PRI',
        ], $rows);
    }

    private function getPgsqlColumns(string $table): array
    {
        $sql = "
            SELECT c.column_name, c.data_type, c.is_nullable,
                   BOOL_OR(tc.constraint_type = 'PRIMARY KEY') AS is_pk
            FROM information_schema.columns c
            LEFT JOIN information_schema.key_column_usage kcu
                   ON c.column_name = kcu.column_name
                  AND c.table_name  = kcu.table_name
                  AND c.table_schema = kcu.table_schema
            LEFT JOIN information_schema.table_constraints tc
                   ON kcu.constraint_name  = tc.constraint_name
                  AND kcu.table_schema     = tc.table_schema
            WHERE c.table_name = " . $this->pdo->quote($table) . "
              AND c.table_schema = 'public'
            GROUP BY c.column_name, c.data_type, c.is_nullable, c.ordinal_position
            ORDER BY c.ordinal_position
        ";
        $rows = $this->pdo->query($sql)->fetchAll();

        return array_map(fn ($r) => [
            'name'     => $r['column_name'],
            'type'     => $r['data_type'],
            'nullable' => $r['is_nullable'] === 'YES',
            'pk'       => (bool) $r['is_pk'],
        ], $rows);
    }

    private function getSqlsrvColumns(string $table): array
    {
        $sql = "
            SELECT c.COLUMN_NAME, c.DATA_TYPE, c.IS_NULLABLE,
                   CASE WHEN kcu.COLUMN_NAME IS NOT NULL THEN 1 ELSE 0 END AS IS_PK
            FROM INFORMATION_SCHEMA.COLUMNS c
            LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
                   ON c.COLUMN_NAME = kcu.COLUMN_NAME AND c.TABLE_NAME = kcu.TABLE_NAME
            LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
                   ON kcu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME AND tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
            WHERE c.TABLE_NAME = " . $this->pdo->quote($table) . "
            ORDER BY c.ORDINAL_POSITION
        ";
        $rows = $this->pdo->query($sql)->fetchAll();

        return array_map(fn ($r) => [
            'name'     => $r['COLUMN_NAME'],
            'type'     => $r['DATA_TYPE'],
            'nullable' => $r['IS_NULLABLE'] === 'YES',
            'pk'       => (bool) $r['IS_PK'],
        ], $rows);
    }

    // ── Foreign Keys ────────────────────────────────────────────────────────

    private function getForeignKeys(string $table): array
    {
        try {
            return match ($this->driver) {
                'pgsql'  => $this->getPgsqlFks($table),
                'sqlsrv' => $this->getSqlsrvFks($table),
                default  => $this->getMysqlFks($table),
            };
        } catch (\Throwable) {
            return [];
        }
    }

    private function getMysqlFks(string $table): array
    {
        $sql = "
            SELECT COLUMN_NAME AS column_name,
                   REFERENCED_TABLE_NAME AS referenced_table,
                   REFERENCED_COLUMN_NAME AS referenced_column
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = " . $this->pdo->quote($this->config['db_name']) . "
              AND TABLE_NAME = " . $this->pdo->quote($table) . "
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        return $this->pdo->query($sql)->fetchAll() ?: [];
    }

    private function getPgsqlFks(string $table): array
    {
        $sql = "
            SELECT kcu.column_name, ccu.table_name AS referenced_table, ccu.column_name AS referenced_column
            FROM information_schema.table_constraints tc
            JOIN information_schema.key_column_usage kcu
                ON tc.constraint_name = kcu.constraint_name AND tc.table_schema = kcu.table_schema
            JOIN information_schema.constraint_column_usage ccu
                ON tc.constraint_name = ccu.constraint_name AND tc.table_schema = ccu.table_schema
            WHERE tc.table_name = " . $this->pdo->quote($table) . "
              AND tc.constraint_type = 'FOREIGN KEY'
        ";

        return $this->pdo->query($sql)->fetchAll() ?: [];
    }

    private function getSqlsrvFks(string $table): array
    {
        // ใช้ sys.tables JOIN แทน OBJECT_ID() string interpolation เพื่อป้องกัน SQL injection
        $sql = "
            SELECT fk_col.name AS column_name, ref_t.name AS referenced_table, ref_col.name AS referenced_column
            FROM sys.foreign_key_columns fkc
            JOIN sys.columns fk_col
                ON fkc.parent_object_id = fk_col.object_id AND fkc.parent_column_id = fk_col.column_id
            JOIN sys.tables parent_t ON fkc.parent_object_id = parent_t.object_id
            JOIN sys.tables ref_t    ON fkc.referenced_object_id = ref_t.object_id
            JOIN sys.columns ref_col
                ON fkc.referenced_object_id = ref_col.object_id AND fkc.referenced_column_id = ref_col.column_id
            WHERE parent_t.name = ?
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$table]);

        return $stmt->fetchAll() ?: [];
    }

    // ── Row Count & Sample ──────────────────────────────────────────────────

    private function getRowCount(string $table): int
    {
        try {
            $row = $this->pdo->query('SELECT COUNT(*) AS cnt FROM ' . $this->qi($table))->fetch();

            return (int) ($row['cnt'] ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function getSample(string $table): array
    {
        try {
            $qt  = $this->qi($table);
            $sql = $this->driver === 'sqlsrv'
                ? "SELECT TOP 3 * FROM {$qt}"
                : "SELECT * FROM {$qt} LIMIT 3";

            return array_map(
                fn ($row) => $this->sanitizeSampleRow($row),
                $this->pdo->query($sql)->fetchAll()
            );
        } catch (\Throwable) {
            return [];
        }
    }

    private function sanitizeSampleRow(array $row): array
    {
        $pii = '/password|passwd|pwd|secret|token|phone|card|ssn|national_id/i';
        $result = [];
        foreach ($row as $key => $value) {
            $result[$key] = preg_match($pii, (string) $key) ? '[REDACTED]' : $value;
        }

        return $result;
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function buildPdo(): PDO
    {
        $h = $this->config['db_host'];
        $p = $this->config['db_port'];
        $d = $this->config['db_name'];
        $u = $this->config['db_user'];
        $pw = $this->config['db_password'] ?? '';

        $dsn = match ($this->driver) {
            'sqlsrv' => "sqlsrv:Server={$h},{$p};Database={$d};TrustServerCertificate=1;Encrypt=0",
            'pgsql'  => "pgsql:host={$h};port={$p};dbname={$d}",
            default  => "mysql:host={$h};port={$p};dbname={$d};charset=utf8mb4",
        };

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        if ($this->driver !== 'sqlsrv') {
            $options[PDO::ATTR_TIMEOUT] = 10;
        }

        return new PDO($dsn, $u, $pw, $options);
    }

    private function qi(string $name): string
    {
        $q = $this->driver === 'mysql' ? '`' : '"';

        return implode('.', array_map(
            fn ($p) => $q . str_replace($q, '', $p) . $q,
            explode('.', $name)
        ));
    }
}
