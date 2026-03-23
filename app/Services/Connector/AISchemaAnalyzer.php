<?php

namespace App\Services\Connector;

use Illuminate\Support\Facades\Http;

class AISchemaAnalyzer
{
    public function __construct(
        private readonly string $apiKey = '',
        private readonly string $model = 'claude-opus-4-6'
    ) {}

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, string>  $sourceFiles  path → content
     * @param  array<string, mixed>|null  $ruleHint
     * @return array<string, mixed>
     */
    public function analyze(array $schema, array $sourceFiles = [], ?array $ruleHint = null): array
    {
        $trimmedSchema = $this->trimSchema($schema);
        $prompt        = $this->buildPrompt($trimmedSchema, $sourceFiles, $ruleHint);

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(90)->post('https://api.anthropic.com/v1/messages', [
            'model'       => $this->model,
            'max_tokens'  => 2048,
            'tools'       => [$this->buildTool()],
            'tool_choice' => ['type' => 'tool', 'name' => 'suggest_connector_config'],
            'messages'    => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Claude API error: ' . $response->status() . ' ' . $response->body());
        }

        foreach ($response->json('content', []) as $block) {
            if (($block['type'] ?? '') === 'tool_use' && $block['name'] === 'suggest_connector_config') {
                return $block['input'];
            }
        }

        throw new \RuntimeException('Claude API did not return a tool_use response');
    }

    /**
     * ตัด schema ให้กระชับก่อนส่ง AI: เก็บ sample ไว้แค่ 1 แถว และตาราง > 10 col จะไม่ส่ง sample
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function trimSchema(array $schema): array
    {
        $trimmed = [];

        foreach ($schema as $table => $info) {
            $colCount = count($info['columns'] ?? []);
            $entry    = $info;

            if ($colCount > 10) {
                unset($entry['sample']);
            } elseif (! empty($entry['sample']) && is_array($entry['sample'])) {
                $entry['sample'] = array_slice($entry['sample'], 0, 1);
            }

            $trimmed[$table] = $entry;
        }

        return $trimmed;
    }

    private function buildPrompt(array $schema, array $sourceFiles, ?array $ruleHint): string
    {
        $schemaJson = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $sourceSection = '';
        if (! empty($sourceFiles)) {
            $sourceSection = "\n\n## Source Code Files\n";
            foreach ($sourceFiles as $path => $content) {
                $sourceSection .= "\n### {$path}\n```\n{$content}\n```\n";
            }
        }

        $ruleSection = '';
        if ($ruleHint !== null) {
            $ruleSection = "\n\n## Rule-Based Pre-Analysis (ใช้เป็นจุดเริ่มต้น — ตรวจสอบและปรับปรุงให้แม่นยำ)\n" .
                json_encode($ruleHint, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return <<<PROMPT
คุณคือผู้เชี่ยวชาญด้านการวิเคราะห์โครงสร้างฐานข้อมูลและระบบ authentication/authorization

วิเคราะห์ schema ด้านล่างแล้วแนะนำการตั้งค่า Connector สำหรับระบบ UCM (User Centralized Management)

## ข้อควรระวัง: naming conventions ต่างกันในแต่ละระบบ

แต่ละองค์กรตั้งชื่อต่างกัน — ให้วิเคราะห์จากโครงสร้างและ FK ไม่ใช่แค่ชื่อ

**ชื่อตาราง user ที่พบบ่อย**:
`users`, `employees`, `members`, `accounts`, `staff`, `persons`, `operators`,
`UserMgnt_Users`, `HRM_Employee`, `tbl_user`, `sys_accounts`, `app_members`, `EmpMaster`

**ชื่อตาราง junction ที่พบบ่อย**:
`user_roles`, `user_permissions`, `user_grants`, `emp_access`, `member_groups`,
`UserMgnt_UserGrant`, `HRM_EmpRole`, `tbl_user_perm`, `AccessMap`, `RoleAssignment`

**คอลัมน์ password** (สัญญาณแข็งแกร่งที่สุด — เฉพาะ user table):
`password`, `passwd`, `pwd`, `pass_hash`, `user_password`, `login_password`,
`encoded_pass`, `md5_pass`, `sha_pass`, `crypt_pass`, `pw_hash`

**คอลัมน์ identifier/login**:
`username`, `user_name`, `login`, `login_name`, `login_id`, `uname`, `user_code`,
`employee_id`, `emp_no`, `emp_id`, `emp_code`, `staff_id`, `staff_code`,
`nt_account`, `ad_username`, `domain_user`, `ldap_account`, `account_no`, `member_code`

**คอลัมน์ email**:
`email`, `mail`, `email_address`, `email_addr`, `e_mail`, `user_email`, `contact_email`

**คอลัมน์ status**:
`status`, `is_active`, `active`, `enabled`, `flag_active`, `active_flag`,
`user_status`, `del_flag`, `is_deleted`, `locked`, `is_disabled`, `suspended`

**คอลัมน์ชื่อบุคคล**:
`full_name`, `fullname`, `name`, `display_name`, `name_th`, `name_en`,
`first_name`, `firstname`, `fname`, `thai_name`, `employee_name`, `given_name`

## UCM Connector — คำอธิบาย fields ที่ต้องการ

### 1. user_table — ตาราง Users
- `table`: ตารางที่เก็บข้อมูล user — ดูจาก: มีคอลัมน์ password, email, identifier พร้อมกัน
- `mapping.identifier`: คอลัมน์ login username หรือ employee code
- `mapping.name`: คอลัมน์ชื่อเต็มของ user
- `mapping.email`: คอลัมน์ email
- `mapping.status`: คอลัมน์สถานะ active/inactive

### 2. permission — รูปแบบการจัดการสิทธิ์ (เลือก 1 mode)

**mode = junction** (แนะนำมากที่สุด):
  ใช้เมื่อมีตาราง mapping แยกต่างหากที่เชื่อม user กับ permission
  เช่น `user_roles(user_id, role_id)` หรือ `UserGrant(user_id, pg_id, site_id)`
  - `table`: ชื่อตาราง junction
  - `user_fk_col`: คอลัมน์ FK ที่อ้างอิง user table (เช่น user_id, emp_id, n_id)
  - `value_col`: **คอลัมน์ FK แรกที่ไม่ใช่ user FK** — ค่าหลักของสิทธิ์ (เช่น role_id, pg_id)
  - `composite_cols`: FK เสริมที่เหลือ (ถ้าตาราง junction มี FK > 2 ตัว)
    ตัวอย่าง: junction มี user_id, pg_id, s_id → value_col=pg_id, composite_cols=[{col:"s_id", master_table:"Sites"}]
    ถ้ามีแค่ 2 FK → composite_cols=[] (ว่าง)

**mode = column** (permission เก็บในคอลัมน์บน user table โดยตรง):
  เช่น `users.role`, `users.access_level`, `users.user_type`, `employees.level`
  - `column`: ชื่อคอลัมน์บน user table

**mode = manual** (ไม่สามารถระบุรูปแบบได้):
  ใช้เมื่อ schema ซับซ้อนเกินกว่าจะวิเคราะห์ได้อัตโนมัติ

### 3. master_tables — ตาราง reference/lookup
ตาราง lookup ที่มีคอลัมน์น้อย (≤12) และแถวข้อมูลปานกลาง (1–5,000)
เช่น roles, departments, page_groups, sites, categories, document_types
**ข้ามตาราง**: log, audit, history, session, token, queue, migration, temp

## Database Schema
```json
{$schemaJson}
```
{$sourceSection}{$ruleSection}

## คำสั่ง
วิเคราะห์อย่างละเอียด โดยให้ความสำคัญกับโครงสร้างตาราง (FK constraints, จำนวนคอลัมน์, row count) มากกว่าชื่อตาราง
จากนั้นเรียก tool `suggest_connector_config` พร้อม:
- `confidence` 0–100 สะท้อนความมั่นใจจริงๆ
- `reasons` อธิบายหลักฐานที่ใช้ตัดสิน (ภาษาไทยหรืออังกฤษ)
- junction mode: ระบุ composite_cols ทุกตัวที่พบ (ถ้าไม่มีให้ส่ง [])
PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTool(): array
    {
        return [
            'name'        => 'suggest_connector_config',
            'description' => 'แนะนำการตั้งค่า Connector Config สำหรับ UCM',
            'input_schema' => [
                'type'       => 'object',
                'required'   => ['user_table', 'permission'],
                'properties' => [
                    'user_table' => [
                        'type'       => 'object',
                        'required'   => ['table', 'confidence', 'reasons'],
                        'properties' => [
                            'table'      => ['type' => 'string', 'description' => 'ชื่อตาราง user'],
                            'confidence' => ['type' => 'integer', 'description' => 'ความมั่นใจ 0–100'],
                            'reasons'    => ['type' => 'array', 'items' => ['type' => 'string']],
                            'mapping'    => [
                                'type'       => 'object',
                                'properties' => [
                                    'identifier' => ['type' => 'string'],
                                    'name'       => ['type' => 'string'],
                                    'email'      => ['type' => 'string'],
                                    'status'     => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                    'permission' => [
                        'type'       => 'object',
                        'required'   => ['mode', 'confidence', 'reasons'],
                        'properties' => [
                            'mode'        => ['type' => 'string', 'enum' => ['junction', 'column', 'manual']],
                            'confidence'  => ['type' => 'integer'],
                            'reasons'     => ['type' => 'array', 'items' => ['type' => 'string']],
                            'table'       => ['type' => 'string'],
                            'user_fk_col' => ['type' => 'string'],
                            'value_col'   => ['type' => 'string'],
                            'label_col'   => ['type' => 'string'],
                            'group_col'   => ['type' => 'string'],
                            'column'         => ['type' => 'string', 'description' => 'สำหรับ column mode'],
                            'composite_cols' => [
                                'type'        => 'array',
                                'description' => 'คอลัมน์ FK เสริมในตาราง junction ที่อ้างอิง master tables เพิ่มเติม',
                                'items'       => [
                                    'type'       => 'object',
                                    'properties' => [
                                        'col'              => ['type' => 'string', 'description' => 'ชื่อคอลัมน์ใน junction table'],
                                        'master_table'     => ['type' => 'string'],
                                        'master_label_col' => ['type' => 'string'],
                                    ],
                                    'required' => ['col'],
                                ],
                            ],
                        ],
                    ],
                    'master_tables' => [
                        'type'  => 'array',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'table'   => ['type' => 'string'],
                                'reasons' => ['type' => 'array', 'items' => ['type' => 'string']],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
