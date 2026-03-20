<?php

namespace Database\Seeders;

use App\Models\System;
use App\Models\SystemPermission;
use Illuminate\Database\Seeder;

class EFilingSeeder extends Seeder
{
    public function run(): void
    {
        $system = System::updateOrCreate(
            ['slug' => 'efiling'],
            [
                'name'          => 'E-Filing',
                'description'   => 'ระบบจัดเก็บเอกสารการบิน',
                'adapter_class' => \App\Adapters\EFilingAdapter::class,
                'db_host'       => 'BFSAPPSDB02.BFSASIA.COM',
                'db_port'       => '1433',
                'db_name'       => 'EFiling',
                'db_user'       => 'efiling_user',
                'db_password'   => 'efiling@Bfs2023',
                'color'         => '#0ea5e9', // สีฟ้า
                'icon'          => 'folder',
                'is_active'     => true,
            ]
        );

        $permissions = [
            // Role (exclusive — เลือกได้แค่ 1)
            ['key' => 'role_admin',    'label' => 'Admin',      'group' => 'Role', 'description' => 'จัดการทุกอย่างได้เต็มที่',              'sort_order' => 1, 'is_exclusive' => true],
            ['key' => 'role_editable', 'label' => 'Editable',   'group' => 'Role', 'description' => 'อัปโหลดและแก้ไขเอกสารได้',             'sort_order' => 2, 'is_exclusive' => true],
            ['key' => 'role_readonly', 'label' => 'Read Only',  'group' => 'Role', 'description' => 'ดูเอกสารได้อย่างเดียว',               'sort_order' => 3, 'is_exclusive' => true],

            // Department (non-exclusive — เลือกได้หลายอัน)
            ['key' => 'dep_ramp',  'label' => 'RAMP',  'group' => 'Department', 'description' => 'แผนก Ramp',              'sort_order' => 10, 'is_exclusive' => false],
            ['key' => 'dep_pax',   'label' => 'PAX',   'group' => 'Department', 'description' => 'แผนก Passenger Service', 'sort_order' => 11, 'is_exclusive' => false],
            ['key' => 'dep_cargo', 'label' => 'CARGO', 'group' => 'Department', 'description' => 'แผนก Cargo',             'sort_order' => 12, 'is_exclusive' => false],

            // Document Category (non-exclusive)
            ['key' => 'cat_ahl',             'label' => 'AHL',             'group' => 'Document Category', 'description' => '', 'sort_order' => 20, 'is_exclusive' => false],
            ['key' => 'cat_dpr',             'label' => 'DPR',             'group' => 'Document Category', 'description' => '', 'sort_order' => 21, 'is_exclusive' => false],
            ['key' => 'cat_flight_dispatch', 'label' => 'Flight Dispatch', 'group' => 'Document Category', 'description' => '', 'sort_order' => 22, 'is_exclusive' => false],
            ['key' => 'cat_flight_document', 'label' => 'Flight Document', 'group' => 'Document Category', 'description' => '', 'sort_order' => 23, 'is_exclusive' => false],
            ['key' => 'cat_marshaller',      'label' => 'Marshaller',      'group' => 'Document Category', 'description' => '', 'sort_order' => 24, 'is_exclusive' => false],
            ['key' => 'cat_trc',             'label' => 'TRC',             'group' => 'Document Category', 'description' => '', 'sort_order' => 25, 'is_exclusive' => false],
            ['key' => 'cat_load_master',     'label' => 'Load Master',     'group' => 'Document Category', 'description' => '', 'sort_order' => 26, 'is_exclusive' => false],
            ['key' => 'cat_load_control',    'label' => 'Load Control',    'group' => 'Document Category', 'description' => '', 'sort_order' => 27, 'is_exclusive' => false],
            ['key' => 'cat_flight_file',     'label' => 'Flight File',     'group' => 'Document Category', 'description' => '', 'sort_order' => 28, 'is_exclusive' => false],
            ['key' => 'cat_fwd',             'label' => 'FWD',             'group' => 'Document Category', 'description' => '', 'sort_order' => 29, 'is_exclusive' => false],
            ['key' => 'cat_ohd',             'label' => 'OHD',             'group' => 'Document Category', 'description' => '', 'sort_order' => 30, 'is_exclusive' => false],
            ['key' => 'cat_psc_report',      'label' => 'PSC Report',      'group' => 'Document Category', 'description' => '', 'sort_order' => 31, 'is_exclusive' => false],
            ['key' => 'cat_sale_report',     'label' => 'Sale Report',     'group' => 'Document Category', 'description' => '', 'sort_order' => 32, 'is_exclusive' => false],
            ['key' => 'cat_work_order',      'label' => 'Work Order',      'group' => 'Document Category', 'description' => '', 'sort_order' => 33, 'is_exclusive' => false],
            ['key' => 'cat_cleaning',        'label' => 'Cleaning',        'group' => 'Document Category', 'description' => '', 'sort_order' => 34, 'is_exclusive' => false],
            ['key' => 'cat_sorting',         'label' => 'Sorting',         'group' => 'Document Category', 'description' => '', 'sort_order' => 35, 'is_exclusive' => false],
            ['key' => 'cat_loading',         'label' => 'Loading',         'group' => 'Document Category', 'description' => '', 'sort_order' => 36, 'is_exclusive' => false],
            ['key' => 'cat_admin',           'label' => 'Admin',           'group' => 'Document Category', 'description' => '', 'sort_order' => 37, 'is_exclusive' => false],
            ['key' => 'cat_airline_witness', 'label' => 'Airline Witness', 'group' => 'Document Category', 'description' => '', 'sort_order' => 38, 'is_exclusive' => false],
        ];

        foreach ($permissions as $perm) {
            SystemPermission::updateOrCreate(
                ['system_id' => $system->id, 'key' => $perm['key']],
                [
                    'label'        => $perm['label'],
                    'group'        => $perm['group'],
                    'description'  => $perm['description'],
                    'sort_order'   => $perm['sort_order'],
                    'is_exclusive' => $perm['is_exclusive'],
                ]
            );
        }

        $this->command->info("✓ E-Filing seeded — {$system->permissions()->count()} permissions");
    }
}
