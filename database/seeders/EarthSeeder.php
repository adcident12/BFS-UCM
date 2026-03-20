<?php

namespace Database\Seeders;

use App\Models\System;
use App\Models\SystemPermission;
use Illuminate\Database\Seeder;

class EarthSeeder extends Seeder
{
    public function run(): void
    {
        $system = System::updateOrCreate(
            ['slug' => 'earth'],
            [
                'name'          => 'EARTH - Flight Operations System',
                'description'   => 'ระบบปฏิบัติการภาคพื้นสำหรับ Bangkok Flight Services — Daily Flight, PAX, RAMP, Accounting',
                'adapter_class' => \App\Adapters\EarthAdapter::class,
                'db_host'       => 'BFSAPPSDB01.BFSASIA.COM',
                'db_port'       => '1433',
                'db_name'       => 'BFS_FlightDB',
                'db_user'       => 'earth_user',
                'db_password'   => 'earth@Bfs2015',
                'api_url'       => null,
                'api_token'     => null,
                'color'         => '#0ea5e9',
                'icon'          => 'globe-alt',
                'is_active'     => true,
            ]
        );

        /**
         * แต่ละ group ของ earth ใช้ UserMgnt_PageGroup.group_name เป็น key
         * remote_value format: "{group_name}:{s_id}"
         *   s_id=1 = Read Only (ดูได้อย่างเดียว)
         *   s_id=2 = Editable  (แก้ไขได้)
         *   s_id=3 = Denied    (ไม่มีสิทธิ์)
         *
         * is_exclusive=true → ต่อ 1 user เลือกได้แค่อันเดียวใน group เดียวกัน
         * sort_order: edit=X0, read=X1, deny=X2 (เรียงภายใน group)
         */
        $permissions = [
            // ── Master Schedule ───────────────────────────────────────
            ['key' => 'master_edit', 'label' => 'Master Schedule — Editable', 'group' => 'Master Schedule', 'remote_value' => 'Master Schedule:2', 'is_exclusive' => true, 'sort_order' => 1],
            ['key' => 'master_read', 'label' => 'Master Schedule — Read Only', 'group' => 'Master Schedule', 'remote_value' => 'Master Schedule:1', 'is_exclusive' => true, 'sort_order' => 2],
            ['key' => 'master_deny', 'label' => 'Master Schedule — Denied', 'group' => 'Master Schedule', 'remote_value' => 'Master Schedule:3', 'is_exclusive' => true, 'sort_order' => 3],

            // ── Daily Flight ──────────────────────────────────────────
            ['key' => 'daily_edit',  'label' => 'Daily Flight — Editable',   'group' => 'Daily Flight',   'remote_value' => 'Daily Flight:2',   'is_exclusive' => true, 'sort_order' => 11],
            ['key' => 'daily_read',  'label' => 'Daily Flight — Read Only',   'group' => 'Daily Flight',   'remote_value' => 'Daily Flight:1',   'is_exclusive' => true, 'sort_order' => 12],
            ['key' => 'daily_deny',  'label' => 'Daily Flight — Denied',  'group' => 'Daily Flight',   'remote_value' => 'Daily Flight:3',   'is_exclusive' => true, 'sort_order' => 13],

            // ── Basic Data (ROC) ──────────────────────────────────────
            ['key' => 'basic_edit',  'label' => 'Basic Data — Editable',    'group' => 'Basic Data',     'remote_value' => 'Basic Data:2',     'is_exclusive' => true, 'sort_order' => 21],
            ['key' => 'basic_read',  'label' => 'Basic Data — Read Only',    'group' => 'Basic Data',     'remote_value' => 'Basic Data:1',     'is_exclusive' => true, 'sort_order' => 22],
            ['key' => 'basic_deny',  'label' => 'Basic Data — Denied',   'group' => 'Basic Data',     'remote_value' => 'Basic Data:3',     'is_exclusive' => true, 'sort_order' => 23],

            // ── PAX ───────────────────────────────────────────────────
            ['key' => 'pax_edit',    'label' => 'PAX — Editable',           'group' => 'PAX',            'remote_value' => 'PAX:2',            'is_exclusive' => true, 'sort_order' => 31],
            ['key' => 'pax_read',    'label' => 'PAX — Read Only',           'group' => 'PAX',            'remote_value' => 'PAX:1',            'is_exclusive' => true, 'sort_order' => 32],
            ['key' => 'pax_deny',    'label' => 'PAX — Denied',          'group' => 'PAX',            'remote_value' => 'PAX:3',            'is_exclusive' => true, 'sort_order' => 33],

            // ── RAMP ──────────────────────────────────────────────────
            ['key' => 'ramp_edit',   'label' => 'RAMP — Editable',         'group' => 'RAMP',           'remote_value' => 'RAMP:2',           'is_exclusive' => true, 'sort_order' => 41],
            ['key' => 'ramp_read',   'label' => 'RAMP — Read Only',         'group' => 'RAMP',           'remote_value' => 'RAMP:1',           'is_exclusive' => true, 'sort_order' => 42],
            ['key' => 'ramp_deny',   'label' => 'RAMP — Denied',        'group' => 'RAMP',           'remote_value' => 'RAMP:3',           'is_exclusive' => true, 'sort_order' => 43],

            // ── PG ROC ────────────────────────────────────────────────
            ['key' => 'pg_edit',     'label' => 'PG ROC — Editable',       'group' => 'PG ROC',         'remote_value' => 'PG ROC:2',         'is_exclusive' => true, 'sort_order' => 51],
            ['key' => 'pg_read',     'label' => 'PG ROC — Read Only',       'group' => 'PG ROC',         'remote_value' => 'PG ROC:1',         'is_exclusive' => true, 'sort_order' => 52],
            ['key' => 'pg_deny',     'label' => 'PG ROC — Denied',      'group' => 'PG ROC',         'remote_value' => 'PG ROC:3',         'is_exclusive' => true, 'sort_order' => 53],

            // ── Finance (Account) ─────────────────────────────────────
            ['key' => 'account_edit','label' => 'Finance — Editable',       'group' => 'Finance',        'remote_value' => 'Finance:2',        'is_exclusive' => true, 'sort_order' => 61],
            ['key' => 'account_read','label' => 'Finance — Read Only',       'group' => 'Finance',        'remote_value' => 'Finance:1',        'is_exclusive' => true, 'sort_order' => 62],
            ['key' => 'account_deny','label' => 'Finance — Denied',      'group' => 'Finance',        'remote_value' => 'Finance:3',        'is_exclusive' => true, 'sort_order' => 63],

            // ── Admin ─────────────────────────────────────────────────
            ['key' => 'admin_edit',  'label' => 'Admin — Editable',         'group' => 'Admin',          'remote_value' => 'Admin:2',          'is_exclusive' => true, 'sort_order' => 71],
            ['key' => 'admin_read',  'label' => 'Admin — Read Only',         'group' => 'Admin',          'remote_value' => 'Admin:1',          'is_exclusive' => true, 'sort_order' => 72],
            ['key' => 'admin_deny',  'label' => 'Admin — Denied',        'group' => 'Admin',          'remote_value' => 'Admin:3',          'is_exclusive' => true, 'sort_order' => 73],

            // ── Gantt Assignment ──────────────────────────────────────
            ['key' => 'gantt_assignment_edit', 'label' => 'Gantt Assignment — Editable', 'group' => 'Gantt Assignment', 'remote_value' => 'Gantt Assignment:2', 'is_exclusive' => true, 'sort_order' => 81],
            ['key' => 'gantt_assignment_read', 'label' => 'Gantt Assignment — Read Only', 'group' => 'Gantt Assignment', 'remote_value' => 'Gantt Assignment:1', 'is_exclusive' => true, 'sort_order' => 82],
            ['key' => 'gantt_assignment_deny', 'label' => 'Gantt Assignment — Denied', 'group' => 'Gantt Assignment', 'remote_value' => 'Gantt Assignment:3', 'is_exclusive' => true, 'sort_order' => 83],

            // ── Pushback Assignment ───────────────────────────────────
            ['key' => 'pushback_assignment_edit', 'label' => 'Pushback Assignment — Editable', 'group' => 'Pushback Assignment', 'remote_value' => 'Pushback Assignment:2', 'is_exclusive' => true, 'sort_order' => 91],
            ['key' => 'pushback_assignment_read', 'label' => 'Pushback Assignment — Read Only', 'group' => 'Pushback Assignment', 'remote_value' => 'Pushback Assignment:1', 'is_exclusive' => true, 'sort_order' => 92],
            ['key' => 'pushback_assignment_deny', 'label' => 'Pushback Assignment — Denied', 'group' => 'Pushback Assignment', 'remote_value' => 'Pushback Assignment:3', 'is_exclusive' => true, 'sort_order' => 93],

            // ── GSE Decoded Service Setup ─────────────────────────────
            ['key' => 'gse_decoded_service_setup_edit', 'label' => 'GSE Decoded Service Setup — Editable', 'group' => 'GSE Decoded Service Setup', 'remote_value' => 'GSE Decoded Service Setup:2', 'is_exclusive' => true, 'sort_order' => 101],
            ['key' => 'gse_decoded_service_setup_read', 'label' => 'GSE Decoded Service Setup — Read Only', 'group' => 'GSE Decoded Service Setup', 'remote_value' => 'GSE Decoded Service Setup:1', 'is_exclusive' => true, 'sort_order' => 102],
            ['key' => 'gse_decoded_service_setup_deny', 'label' => 'GSE Decoded Service Setup — Denied', 'group' => 'GSE Decoded Service Setup', 'remote_value' => 'GSE Decoded Service Setup:3', 'is_exclusive' => true, 'sort_order' => 103],

            // ── BSC Decoded Services setup ────────────────────────────
            ['key' => 'bsc_decoded_services_setup_edit', 'label' => 'BSC Decoded Services setup — Editable', 'group' => 'BSC Decoded Services setup', 'remote_value' => 'BSC Decoded Services setup:2', 'is_exclusive' => true, 'sort_order' => 111],
            ['key' => 'bsc_decoded_services_setup_read', 'label' => 'BSC Decoded Services setup — Read Only', 'group' => 'BSC Decoded Services setup', 'remote_value' => 'BSC Decoded Services setup:1', 'is_exclusive' => true, 'sort_order' => 112],
            ['key' => 'bsc_decoded_services_setup_deny', 'label' => 'BSC Decoded Services setup — Denied', 'group' => 'BSC Decoded Services setup', 'remote_value' => 'BSC Decoded Services setup:3', 'is_exclusive' => true, 'sort_order' => 113],

            // ── FLIGHT DOCUMENT Decoded Service Settings ──────────────
            ['key' => 'flight_document_decoded_service_settings_edit', 'label' => 'FLIGHT DOCUMENT Decoded Service Settings — Editable', 'group' => 'FLIGHT DOCUMENT Decoded Service Settings', 'remote_value' => 'FLIGHT DOCUMENT Decoded Service Settings:2', 'is_exclusive' => true, 'sort_order' => 121],
            ['key' => 'flight_document_decoded_service_settings_read', 'label' => 'FLIGHT DOCUMENT Decoded Service Settings — Read Only', 'group' => 'FLIGHT DOCUMENT Decoded Service Settings', 'remote_value' => 'FLIGHT DOCUMENT Decoded Service Settings:1', 'is_exclusive' => true, 'sort_order' => 122],
            ['key' => 'flight_document_decoded_service_settings_deny', 'label' => 'FLIGHT DOCUMENT Decoded Service Settings — Denied', 'group' => 'FLIGHT DOCUMENT Decoded Service Settings', 'remote_value' => 'FLIGHT DOCUMENT Decoded Service Settings:3', 'is_exclusive' => true, 'sort_order' => 123],

            // ── Update Equipment Status ───────────────────────────────
            ['key' => 'update_equipment_status_edit', 'label' => 'Update Equipment Status — Editable', 'group' => 'Update Equipment Status', 'remote_value' => 'Update Equipment Status:2', 'is_exclusive' => true, 'sort_order' => 131],
            ['key' => 'update_equipment_status_read', 'label' => 'Update Equipment Status — Read Only', 'group' => 'Update Equipment Status', 'remote_value' => 'Update Equipment Status:1', 'is_exclusive' => true, 'sort_order' => 132],
            ['key' => 'update_equipment_status_deny', 'label' => 'Update Equipment Status — Denied', 'group' => 'Update Equipment Status', 'remote_value' => 'Update Equipment Status:3', 'is_exclusive' => true, 'sort_order' => 133],

            // ── Update Equipment Information ──────────────────────────
            ['key' => 'update_equipment_information_edit', 'label' => 'Update Equipment Information — Editable', 'group' => 'Update Equipment Information', 'remote_value' => 'Update Equipment Information:2', 'is_exclusive' => true, 'sort_order' => 141],
            ['key' => 'update_equipment_information_read', 'label' => 'Update Equipment Information — Read Only', 'group' => 'Update Equipment Information', 'remote_value' => 'Update Equipment Information:1', 'is_exclusive' => true, 'sort_order' => 142],
            ['key' => 'update_equipment_information_deny', 'label' => 'Update Equipment Information — Denied', 'group' => 'Update Equipment Information', 'remote_value' => 'Update Equipment Information:3', 'is_exclusive' => true, 'sort_order' => 143],
        ];

        // ลบ permissions เดิม (ชื่อ key เก่าที่ไม่มี _edit/_read) ก่อน upsert ใหม่
        $oldKeys = ['daily_menu','master_menu','pax_menu','ramp_menu','basic_menu','pg_menu','account_menu','admin_menu'];
        $system->permissions()->whereIn('key', $oldKeys)->delete();

        foreach ($permissions as $perm) {
            SystemPermission::updateOrCreate(
                ['system_id' => $system->id, 'key' => $perm['key']],
                [
                    'label'        => $perm['label'],
                    'group'        => $perm['group'],
                    'remote_value' => $perm['remote_value'],
                    'is_exclusive' => $perm['is_exclusive'],
                    'sort_order'   => $perm['sort_order'],
                    'description'  => null,
                ]
            );
        }

        $this->command->info("✓ EARTH System seeded — {$system->permissions()->count()} permissions");
    }
}
