<?php

namespace Database\Seeders;

use App\Models\System;
use App\Models\SystemPermission;
use Illuminate\Database\Seeder;

class RepairSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. สร้าง System record
        $system = System::updateOrCreate(
            ['slug' => 'repair-system'],
            [
                'name'          => 'Repair System',
                'description'   => 'ระบบแจ้งซ่อมและติดตามงาน',
                'adapter_class' => \App\Adapters\RepairSystemAdapter::class,
                'db_host'       => 'BFSAPPSDB03.BFSASIA.COM',
                'db_port'       => '1433',
                'db_name'       => 'repair_notification_system',
                'db_user'       => 'halo_user',
                'db_password'   => 'halo@Bfs2015',
                'color'         => '#f97316', // สีส้ม
                'icon'          => 'wrench',
                'is_active'     => true,
            ]
        );

        // 2. สร้าง permissions
        $permissions = [
            ['key' => 'role_user',     'label' => 'ผู้ใช้งานทั่วไป',  'group' => 'Role', 'description' => 'ดูได้เฉพาะ Ticket ของตัวเอง',                      'sort_order' => 1],
            ['key' => 'role_admin',    'label' => 'ผู้ดูแลระบบ',       'group' => 'Role', 'description' => 'ดู Ticket ทั้งหมด, มอบหมายงาน, เปลี่ยนสถานะ',     'sort_order' => 2],
            ['key' => 'role_superadd', 'label' => 'Super Admin',       'group' => 'Role', 'description' => 'สิทธิ์สูงสุด จัดการผู้ใช้และ role ได้',            'sort_order' => 3],
        ];

        foreach ($permissions as $perm) {
            SystemPermission::updateOrCreate(
                ['system_id' => $system->id, 'key' => $perm['key']],
                [
                    'label'       => $perm['label'],
                    'group'       => $perm['group'],
                    'description' => $perm['description'],
                    'sort_order'  => $perm['sort_order'],
                ]
            );
        }

        $this->command->info("✓ Repair System seeded — {$system->permissions()->count()} permissions");
    }
}
