<?php

namespace App\Services\Connector;

class RuleBasedSuggester
{
    public function __construct(private readonly array $schema) {}

    public function suggest(): array
    {
        $userResult = $this->suggestUserTable();
        $permResult = $this->suggestPermission($userResult['table'] ?? null);

        return [
            'user_table'    => $userResult,
            'permission'    => $permResult,
            'master_tables' => $this->suggestMasterTables($userResult['table'] ?? null, $permResult),
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * ตัด namespace prefix / technical prefix / suffix ออกเพื่อให้เหลือชื่อ "แกน"
     * เช่น "UserMgnt_Users" → "users", "tbl_employee" → "employee", "HRM_EmpMaster" → "empmaster"
     */
    private function coreTableName(string $table): string
    {
        $t = strtolower($table);

        // ตัด namespace prefix รูปแบบ Prefix_Name (เช่น UserMgnt_Users → users)
        if (preg_match('/^[a-z0-9]+_([a-z0-9_]+)$/', $t, $m)) {
            $t = $m[1];
        }

        // ตัด technical prefix ที่พบบ่อย
        $t = preg_replace('/^(tbl_|t_|tb_|sys_|app_|mst_|ref_|cfg_|hr_|hrm_|lms_|crm_|erp_|ad_|ldap_)/', '', $t) ?? $t;

        // ตัด technical suffix ที่พบบ่อย
        $t = preg_replace('/(_tbl|_table|_info|_data|_master|_record|_detail|_list|_mst)$/', '', $t) ?? $t;

        return $t;
    }

    /**
     * หา label column ที่น่าจะเป็นชื่อแสดงผลของตาราง master
     */
    private function guessLabelColForTable(?string $tableName): ?string
    {
        if ($tableName === null || ! isset($this->schema[$tableName])) {
            return null;
        }

        $cols  = $this->schema[$tableName]['columns'] ?? [];
        $lower = array_map('strtolower', array_column($cols, 'name'));
        $map   = array_combine($lower, array_column($cols, 'name'));

        foreach ([
            'name', 'title', 'label', 'description', 'desc', 'caption',
            'name_th', 'name_en', 'display_name', 'full_name',
            'group_name', 'role_name', 'permission_name', 'page_name',
            'page_group_name', 'dept_name', 'department_name',
            'category_name', 'type_name', 'class_name', 'subject_name',
        ] as $c) {
            if (isset($map[$c])) {
                return $map[$c];
            }
        }

        return null;
    }

    /**
     * เดาชื่อตาราง master จากชื่อ FK column
     * เช่น pg_id → PageGroups, role_id → roles, dept_id → departments
     */
    private function guessMasterTableFromFkCol(string $fkCol): ?string
    {
        // ตัด _id suffix ออกเพื่อได้ base name
        $base = (string) preg_replace('/_id$/', '', strtolower($fkCol)); // pg_id → pg

        // สร้างรายการ candidate ที่อาจเป็นชื่อตาราง
        $candidates = array_unique(array_filter([
            $base,
            $base . 's',
            $base . 'es',
            str_replace('_', '', $base),
            str_replace('_', '', $base) . 's',
        ]));

        foreach ($candidates as $candidate) {
            foreach (array_keys($this->schema) as $table) {
                $coreT = $this->coreTableName($table);
                if (strtolower($table) === $candidate || $coreT === $candidate) {
                    return $table;
                }
            }
        }

        return null;
    }

    // ── User Table ──────────────────────────────────────────────────────────

    private function suggestUserTable(): array
    {
        $candidates = [];

        foreach ($this->schema as $table => $info) {
            $score   = 0;
            $reasons = [];
            $cols    = $info['columns'] ?? [];
            $lower   = array_map('strtolower', array_column($cols, 'name'));
            $core    = $this->coreTableName($table);

            // ── Table name signals ─────────────────────────────────────────
            if (preg_match('/^(users?|accounts?|members?|employees?|staffs?|peoples?|persons?|operators?|principals?|admins?)$/', $core)) {
                $score += 30;
                $reasons[] = "ชื่อตาราง '{$table}' (core: {$core}) ตรงกับรูปแบบตาราง user";
            } elseif (preg_match('/user|account|member|employee|staff|personnel|login|operator|person|emp\b/i', $core)
                && ! preg_match('/log|history|audit|session|token|role|perm|grant|right/i', $core)
            ) {
                $score += 15;
                $reasons[] = "ชื่อตาราง '{$table}' มีคำที่เกี่ยวกับ user";
            }

            // ── Column signals ─────────────────────────────────────────────

            // Password — strongest signal (เฉพาะ user table เท่านั้นที่เก็บ hash)
            foreach ($lower as $col) {
                if (preg_match('/passw(or)?d|passwd|pass_hash|pass_word|hashed_pass|secret_hash|encoded_pass|md5_pass|sha_pass|crypt_pass|pw_hash|user_pass/', $col)) {
                    $score += 40;
                    $reasons[] = "มีคอลัมน์ password ({$col})";
                    break;
                }
            }

            // Email
            foreach ($lower as $col) {
                if (preg_match('/^(email|mail|email_addr(ess)?|e_mail|user_email|mail_addr|contact_email|user_mail|email_contact)$/', $col)) {
                    $score += 20;
                    $reasons[] = "มีคอลัมน์ email ({$col})";
                    break;
                }
            }

            // Identifier — exact match (high signal)
            foreach ($lower as $col) {
                if (preg_match(
                    '/^(username|user_name|login|login_name|login_id|uname|user_account|'
                    . 'employee_id|emp_no|emp_id|emp_code|staff_id|staff_code|employee_no|employee_number|employee_code|'
                    . 'person_id|user_code|user_login|user_no|'
                    . 'nt_account|ad_user|ad_username|domain_user|ldap_account|ldap_user|windows_login|'
                    . 'account_no|account_code|member_id|member_code)$/',
                    $col
                )) {
                    $score += 15;
                    $reasons[] = "มีคอลัมน์ identifier ({$col})";
                    break;
                }
            }

            // Name column: บ่งบอกว่าเป็นตาราง person
            foreach ($lower as $col) {
                if (preg_match('/^(full_?name|fullname|full_name_th|full_name_en|display_name|name_th|name_en|thai_name|firstname|lastname|first_name|last_name|f_name|l_name|given_name|surname|employee_name|staff_name)$/', $col)) {
                    $score += 8;
                    $reasons[] = "มีคอลัมน์ชื่อบุคคล ({$col})";
                    break;
                }
            }

            // Row count
            $rowCount = $info['row_count'] ?? 0;
            if ($rowCount > 50) {
                $score += 5;
                $reasons[] = "มีข้อมูล {$rowCount} แถว — น่าจะเป็นตาราง transactional";
            }

            if ($score > 0) {
                // max theoretical = 30+40+20+15+8+5 = 118
                $candidates[$table] = [
                    'table'      => $table,
                    'score'      => $score,
                    'confidence' => min((int) round($score / 118 * 100), 95),
                    'reasons'    => $reasons,
                    'mapping'    => $this->suggestUserMapping(array_column($cols, 'name'), $lower, $info['sample'] ?? []),
                ];
            }
        }

        if (empty($candidates)) {
            return ['table' => null, 'confidence' => 0, 'reasons' => ['ไม่พบตารางที่ตรงกับรูปแบบ user'], 'mapping' => []];
        }

        uasort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);
        $best = array_values($candidates)[0];
        unset($best['score']);

        return $best;
    }

    private function suggestUserMapping(array $colNames, array $lower, array $sample = []): array
    {
        $map     = array_combine($lower, $colNames);
        $mapping = [];

        // Identifier
        foreach ([
            'username', 'user_name', 'login', 'login_name', 'login_id', 'uname',
            'user_code', 'user_login', 'user_no', 'user_account', 'account_id',
            'employee_id', 'emp_no', 'emp_id', 'emp_code', 'staff_id', 'staff_code',
            'employee_no', 'employee_number', 'employee_code', 'person_id',
            'nt_account', 'ad_user', 'ad_username', 'domain_user', 'ldap_account',
            'account_no', 'account_code', 'member_id', 'member_code',
        ] as $c) {
            if (isset($map[$c])) {
                $mapping['identifier'] = $map[$c];
                break;
            }
        }

        // Name
        foreach ([
            'full_name', 'fullname', 'full_name_th', 'full_name_en',
            'display_name', 'name', 'thai_name', 'name_th', 'name_en',
            'employee_name', 'staff_name', 'user_name_display',
            'first_name', 'firstname', 'fname', 'f_name', 'given_name',
        ] as $c) {
            if (isset($map[$c])) {
                $mapping['name'] = $map[$c];
                break;
            }
        }

        // Email
        foreach ([
            'email', 'mail', 'email_address', 'email_addr', 'e_mail',
            'user_email', 'mail_addr', 'contact_email', 'user_mail',
        ] as $c) {
            if (isset($map[$c])) {
                $mapping['email'] = $map[$c];
                break;
            }
        }

        // Department
        foreach ([
            'department', 'dept', 'department_name', 'dept_name', 'department_id',
            'division', 'division_name', 'org_unit', 'section', 'unit', 'branch',
        ] as $c) {
            if (isset($map[$c])) {
                $mapping['department'] = $map[$c];
                break;
            }
        }

        // Status
        foreach ([
            'status', 'is_active', 'active', 'enabled', 'is_enabled', 'flag_active',
            'active_flag', 'user_status', 'acc_status', 'account_status',
            'del_flag', 'is_deleted', 'flag_del', 'deleted',
            'locked', 'is_locked', 'is_disabled', 'suspended', 'flag_del',
        ] as $c) {
            if (isset($map[$c])) {
                $mapping['status'] = $map[$c];
                break;
            }
        }

        // Detect active/inactive values from sample data
        if (isset($mapping['status']) && ! empty($sample)) {
            [$activeVal, $inactiveVal] = $this->detectStatusValues($mapping['status'], $sample);
            if ($activeVal !== null) {
                $mapping['status_active_val'] = $activeVal;
            }
            if ($inactiveVal !== null) {
                $mapping['status_inactive_val'] = $inactiveVal;
            }
        }

        return $mapping;
    }

    /**
     * ตรวจจับค่า active/inactive จาก sample rows โดยเปรียบเทียบกับ known patterns
     *
     * @param  array<int, array<string, mixed>>  $sample
     * @return array{0: string|null, 1: string|null}
     */
    private function detectStatusValues(string $statusCol, array $sample): array
    {
        $knownActive   = ['1', 'true', 'y', 'yes', 'active', 'enable', 'enabled', 'a', 'normal', 'open'];
        $knownInactive = ['0', 'false', 'n', 'no', 'inactive', 'disable', 'disabled', 'i', 'closed', 'blocked', 'suspended'];

        $activeVal   = null;
        $inactiveVal = null;

        foreach ($sample as $row) {
            $val = $row[$statusCol] ?? null;
            if ($val === null) {
                continue;
            }

            $lower = strtolower((string) $val);

            if ($activeVal === null && in_array($lower, $knownActive, true)) {
                $activeVal = (string) $val;
            }

            if ($inactiveVal === null && in_array($lower, $knownInactive, true)) {
                $inactiveVal = (string) $val;
            }

            if ($activeVal !== null && $inactiveVal !== null) {
                break;
            }
        }

        return [$activeVal, $inactiveVal];
    }

    // ── Permission ──────────────────────────────────────────────────────────

    private function suggestPermission(?string $userTable): array
    {
        $junctionCandidates = [];
        $singleColMode      = null;

        foreach ($this->schema as $table => $info) {
            $colNames = array_column($info['columns'], 'name');
            $lower    = array_map('strtolower', $colNames);
            $lowerMap = array_combine($lower, $colNames);
            $core     = $this->coreTableName($table);

            if ($table === $userTable) {
                // ตรวจว่า user table มีคอลัมน์ permission โดยตรงหรือไม่ (Single Column mode)
                foreach ($lower as $col) {
                    if (preg_match('/^(role|roles|permission|permissions|access_level|user_type|user_level|level|user_role|access_type|privilege|authority|rank)$/', $col)) {
                        $singleColMode = [
                            'mode'       => 'column',
                            'column'     => $lowerMap[$col],
                            'confidence' => 65,
                            'reasons'    => ["มีคอลัมน์ '{$col}' บนตาราง user เหมาะกับ Single Column mode"],
                        ];
                        break;
                    }
                }
                continue;
            }

            $fks       = $info['fks'] ?? [];
            $score     = 0;
            $reasons   = [];
            $userFkCol = null;

            // ── Detect FK to user table ────────────────────────────────────

            // 1. Explicit FK constraint
            foreach ($fks as $fk) {
                if (($fk['referenced_table'] ?? null) === $userTable) {
                    $userFkCol = $fk['column_name'] ?? null;
                    $score    += 50;
                    $reasons[] = "มี FK constraint ไปยังตาราง user '{$userTable}'";
                    break;
                }
            }

            // 2. Implicit FK: ดูชื่อคอลัมน์
            if ($userFkCol === null && $userTable !== null) {
                $implicit = $this->findImplicitUserFk($lower, $lowerMap, $userTable);
                if ($implicit !== null) {
                    $userFkCol = $implicit;
                    $score    += 35;
                    $reasons[] = "คอลัมน์ '{$implicit}' คาดว่าเป็น implicit FK ไปยัง user table";
                }
            }

            // ── Column count signal ────────────────────────────────────────
            $colCount = count($colNames);
            if ($colCount <= 4) {
                $score    += 15;
                $reasons[] = "คอลัมน์น้อยมาก ({$colCount}) ลักษณะ pure junction table";
            } elseif ($colCount <= 8) {
                $score    += 8;
                $reasons[] = "คอลัมน์น้อย ({$colCount}) เหมาะกับ junction table";
            }

            // ── Table name signal ──────────────────────────────────────────
            $fullNameMatch = preg_match(
                '/user.*(role|perm|access|privilege|right|grant|group|auth)|
                 (role|perm|access|privilege|right|grant|auth).*(user|member|emp|staff)|
                 ^(role|permission|access|privilege|grant|right|policy|authority|auth_map)s?$/xi',
                $table
            );
            $coreNameMatch = preg_match(
                '/^(user.*(role|perm|access|privilege|right|grant|group)|
                    (role|perm|access|privilege|right|grant).*(user|member|emp))$/xi',
                $core
            );

            if ($fullNameMatch || $coreNameMatch) {
                $score    += 30;
                $reasons[] = "ชื่อตาราง '{$table}' ตรงกับรูปแบบ permission";
            } elseif (preg_match('/role|perm|access|grant|right|policy|privilege|authority/i', $table)) {
                $score    += 15;
                $reasons[] = "ชื่อตาราง '{$table}' มีคำที่เกี่ยวกับ permission";
            }

            if ($score >= 40) {
                $nonUserFkCols = $this->collectNonUserFkCols($fks, $lower, $lowerMap, $userTable, $userFkCol);

                $valueCol      = ! empty($nonUserFkCols) ? $nonUserFkCols[0]['col'] : null;
                $compositeCols = array_slice($nonUserFkCols, 1);

                if ($valueCol === null) {
                    $valueCol = $this->guessValueCol($lower, $lowerMap, $userFkCol);
                }

                $junctionCandidates[$table] = [
                    'mode'           => 'junction',
                    'table'          => $table,
                    'user_fk_col'    => $userFkCol,
                    'value_col'      => $valueCol,
                    'score'          => $score,
                    'confidence'     => min((int) round($score / 95 * 100), 95),
                    'reasons'        => $reasons,
                    'composite_cols' => $compositeCols,
                ];
            }
        }

        if (! empty($junctionCandidates)) {
            uasort($junctionCandidates, fn ($a, $b) => $b['score'] <=> $a['score']);
            $best = array_values($junctionCandidates)[0];
            unset($best['score']);

            return $best;
        }

        if ($singleColMode !== null) {
            return $singleColMode;
        }

        return ['mode' => 'manual', 'confidence' => 30, 'reasons' => ['ไม่พบรูปแบบ permission ที่ชัดเจน — แนะนำตั้งค่า Manual']];
    }

    /**
     * หา implicit FK ไปยัง user table โดยดูชื่อคอลัมน์
     * รองรับรูปแบบหลากหลาย: user_id, uid, emp_id, fk_user, member_key ฯลฯ
     */
    private function findImplicitUserFk(array $lower, array $lowerMap, string $userTable): ?string
    {
        $singular     = rtrim(strtolower($userTable), 's');
        $core         = $this->coreTableName($userTable);
        $singularCore = rtrim($core, 's');

        $candidates = array_unique(array_filter([
            'user_id', 'userid', 'user_key', 'fk_user', 'uid',
            'usr_id', 'usr_key',
            $singular . '_id',
            strtolower($userTable) . '_id',
            $singularCore . '_id',
            $core . '_id',
        ]));

        foreach ($candidates as $candidate) {
            if (isset($lowerMap[$candidate])) {
                return $lowerMap[$candidate];
            }
        }

        // Broader: ลงท้าย _id และมีคำว่า user/member/account/staff/employee/person/emp/operator
        foreach ($lower as $col) {
            if (str_ends_with($col, '_id') && preg_match('/user|member|account|staff|employee|person|emp|operator/i', $col)) {
                return $lowerMap[$col];
            }
        }

        // Broader: ลงท้าย _key/_no/_code และมี keyword
        foreach ($lower as $col) {
            if (preg_match('/_(key|no|code|num)$/', $col) && preg_match('/user|emp|staff|member|account/i', $col)) {
                return $lowerMap[$col];
            }
        }

        return null;
    }

    /**
     * รวม non-user FK columns — ทั้ง explicit (FK constraint) และ implicit (_id columns)
     * พร้อมเดา master_table และ master_label_col จาก schema
     */
    private function collectNonUserFkCols(
        array $fks,
        array $lower,
        array $lowerMap,
        ?string $userTable,
        ?string $userFkCol
    ): array {
        $result   = [];
        $seenCols = [];

        // 1. Explicit non-user FK constraints
        foreach ($fks as $fk) {
            $refTable = $fk['referenced_table'] ?? null;
            $colName  = $fk['column_name'] ?? null;
            if ($refTable === $userTable || $colName === $userFkCol || $colName === null) {
                continue;
            }
            $seenCols[$colName] = true;
            $result[]           = [
                'col'              => $colName,
                'master_table'     => $refTable,
                'master_label_col' => $this->guessLabelColForTable($refTable),
            ];
        }

        // 2. Implicit non-user FKs: columns ending with _id (ไม่ใช่ user FK)
        foreach ($lower as $col) {
            $original = $lowerMap[$col];
            if (isset($seenCols[$original]) || strtolower($original) === strtolower((string) $userFkCol)) {
                continue;
            }
            if (str_ends_with($col, '_id') && $col !== 'id') {
                $guessedTable = $this->guessMasterTableFromFkCol($col);
                $result[]     = [
                    'col'              => $original,
                    'master_table'     => $guessedTable,
                    'master_label_col' => $this->guessLabelColForTable($guessedTable),
                ];
            }
        }

        return $result;
    }

    /**
     * ใช้เมื่อไม่มี FK-based value_col — เดาจากชื่อคอลัมน์
     */
    private function guessValueCol(array $lower, array $lowerMap, ?string $userFkCol): ?string
    {
        $skip = ['id', strtolower((string) $userFkCol)];

        foreach ([
            'permission_id', 'perm_id', 'role_id', 'group_id', 'pg_id', 'page_group_id',
            'access_id', 'right_id', 'auth_id', 'privilege_id',
            'permission', 'role', 'access', 'code', 'value', 'type',
        ] as $c) {
            if (isset($lowerMap[$c]) && ! in_array($c, $skip, true)) {
                return $lowerMap[$c];
            }
        }

        // Last resort: first _id col that isn't user FK
        foreach ($lower as $col) {
            if (str_ends_with($col, '_id') && $col !== 'id' && $col !== strtolower((string) $userFkCol)) {
                return $lowerMap[$col];
            }
        }

        return null;
    }

    // ── Master Tables ───────────────────────────────────────────────────────

    private function suggestMasterTables(?string $userTable, array $permSuggestion): array
    {
        $permTable = $permSuggestion['table'] ?? null;
        $compositeMasters = array_filter(array_column($permSuggestion['composite_cols'] ?? [], 'master_table'));

        // รวม value_col master table ด้วย
        $valueColMaster = null;
        if (! empty($permSuggestion['value_col'])) {
            $valueColMaster = $this->guessMasterTableFromFkCol(
                strtolower($permSuggestion['value_col'])
            );
        }

        $allKnownMasters = array_unique(array_filter(array_merge(
            array_values($compositeMasters),
            $valueColMaster ? [$valueColMaster] : [],
        )));

        $masters = [];

        foreach ($this->schema as $table => $info) {
            if ($table === $userTable || $table === $permTable) {
                continue;
            }

            if (preg_match('/log|audit|history|session|token|queue|job|cache|migration|temp|tmp/i', $table)) {
                continue;
            }

            $colCount = count($info['columns']);
            $rowCount = $info['row_count'] ?? 0;

            // lookup/master table: คอลัมน์น้อย + row count ปานกลาง (ขยาย threshold)
            if ($colCount <= 12 && $rowCount >= 1 && $rowCount <= 5000) {
                $isKnownMaster = in_array($table, $allKnownMasters, true);
                $labelCol      = $this->guessLabelColForTable($table);
                $masters[]     = [
                    'table'         => $table,
                    'row_count'     => $rowCount,
                    'label_col'     => $labelCol,
                    'is_referenced' => $isKnownMaster,
                    'reasons'       => [
                        "คอลัมน์น้อย ({$colCount}), ข้อมูล {$rowCount} แถว — ลักษณะ lookup/reference table"
                        . ($isKnownMaster ? ' (ถูกอ้างอิงจาก junction table)' : ''),
                    ],
                ];
            }
        }

        // referenced masters ก่อน แล้วเรียงตาม row count น้อย→มาก
        usort($masters, function ($a, $b) {
            if ($b['is_referenced'] !== $a['is_referenced']) {
                return (int) $b['is_referenced'] <=> (int) $a['is_referenced'];
            }
            return $a['row_count'] <=> $b['row_count'];
        });

        return $masters;
    }
}
