<?php

/**
 * UCM Internal Feature Access Configuration
 *
 * Each feature defines:
 *   name          — Display name (Thai)
 *   description   — Short description
 *   group         — Sidebar / UI group
 *   default_level — Minimum is_admin level required (0 = ทุกคน, 1 = Admin L1+, 2 = Admin L2 เท่านั้น)
 *   lockable      — false = ห้ามลดลงกว่า default (กันล็อคตัวเอง)
 */
return [

    // ── ทั่วไป ──────────────────────────────────────────────────────
    'dashboard' => [
        'name'          => 'Dashboard',
        'description'   => 'หน้าหลักและสถิติการใช้งาน',
        'group'         => 'ทั่วไป',
        'default_level' => 0,
        'lockable'      => false,
    ],
    'user_list' => [
        'name'          => 'ดูรายชื่อผู้ใช้',
        'description'   => 'เข้าถึงรายชื่อผู้ใช้ทั้งหมดในระบบ',
        'group'         => 'ทั่วไป',
        'default_level' => 0,
        'lockable'      => false,
    ],
    'user_detail' => [
        'name'          => 'ดูรายละเอียดผู้ใช้',
        'description'   => 'เปิดหน้า Profile และข้อมูล Permission ของผู้ใช้แต่ละคน',
        'group'         => 'ทั่วไป',
        'default_level' => 0,
        'lockable'      => false,
    ],
    'system_list' => [
        'name'          => 'ดูรายชื่อระบบที่เชื่อมต่อ',
        'description'   => 'เข้าถึงรายการระบบที่ UCM เชื่อมต่ออยู่',
        'group'         => 'ทั่วไป',
        'default_level' => 0,
        'lockable'      => false,
    ],

    // ── ผู้ใช้ ───────────────────────────────────────────────────────
    'user_import_ldap' => [
        'name'          => 'Import ผู้ใช้จาก LDAP/AD',
        'description'   => 'ค้นหาและเพิ่มผู้ใช้ใหม่จาก Active Directory',
        'group'         => 'ผู้ใช้',
        'default_level' => 1,
        'lockable'      => true,
    ],
    'user_info_edit' => [
        'name'          => 'แก้ไขข้อมูลผู้ใช้',
        'description'   => 'แก้ไขรหัสพนักงานและข้อมูล Profile',
        'group'         => 'ผู้ใช้',
        'default_level' => 2,
        'lockable'      => true,
    ],
    'user_remove' => [
        'name'          => 'ลบ/ถอนผู้ใช้ออกจากระบบ',
        'description'   => 'ลบผู้ใช้ที่ Inactive หรือถอนสิทธิ์ออกจาก UCM',
        'group'         => 'ผู้ใช้',
        'default_level' => 2,
        'lockable'      => true,
    ],
    'user_inactive_report' => [
        'name'          => 'รายงาน Inactive Users',
        'description'   => 'ดูผู้ใช้ที่ไม่ได้ใช้งานตามช่วงเวลาที่กำหนด',
        'group'         => 'ผู้ใช้',
        'default_level' => 1,
        'lockable'      => true,
    ],
    'permission_timeline' => [
        'name'          => 'Permission Timeline ของผู้ใช้',
        'description'   => 'ดูประวัติการได้รับ/ถูกถอนสิทธิ์ของผู้ใช้แต่ละคน',
        'group'         => 'ผู้ใช้',
        'default_level' => 1,
        'lockable'      => true,
    ],

    // ── สิทธิ์ ───────────────────────────────────────────────────────
    'permission_update' => [
        'name'          => 'แก้ไขสิทธิ์ผู้ใช้ในระบบ',
        'description'   => 'มอบ/ถอน Permission ให้ผู้ใช้ในแต่ละระบบที่เชื่อมต่อ',
        'group'         => 'สิทธิ์',
        'default_level' => 1,
        'lockable'      => true,
    ],
    'permission_matrix' => [
        'name'          => 'Permission Matrix Report',
        'description'   => 'ดูตาราง Cross-tab สิทธิ์ของผู้ใช้ทุกคนในทุกระบบ',
        'group'         => 'สิทธิ์',
        'default_level' => 1,
        'lockable'      => true,
    ],
    'permission_center' => [
        'name'          => 'จัดการ Permissions (Permission Center)',
        'description'   => 'จัดการนิยาม Permission ของแต่ละระบบที่เชื่อมต่อ',
        'group'         => 'สิทธิ์',
        'default_level' => 1,
        'lockable'      => true,
    ],

    // ── รายงาน ──────────────────────────────────────────────────────
    'audit_log' => [
        'name'          => 'Audit Log',
        'description'   => 'ดูบันทึกกิจกรรมทั้งหมดในระบบ (Login, Permission เปลี่ยน, ฯลฯ)',
        'group'         => 'รายงาน',
        'default_level' => 1,
        'lockable'      => true,
    ],

    // ── ผู้ดูแลระบบ ─────────────────────────────────────────────────
    'admin_levels' => [
        'name'          => 'จัดการสิทธิ์ Admin',
        'description'   => 'กำหนดระดับ Admin (L0/L1/L2) ให้ผู้ใช้',
        'group'         => 'ผู้ดูแลระบบ',
        'default_level' => 2,
        'lockable'      => true,
    ],
    'ucm_access' => [
        'name'          => 'จัดการสิทธิ์ระบบ UCM',
        'description'   => 'กำหนดว่าแต่ละ Level เข้าถึงฟีเจอร์ใดของ UCM ได้บ้าง',
        'group'         => 'ผู้ดูแลระบบ',
        'default_level' => 2,
        'lockable'      => true,
    ],
    'queue_monitor' => [
        'name'          => 'Queue Monitor',
        'description'   => 'ดูสถานะ Job Queue และ Failed Jobs',
        'group'         => 'ผู้ดูแลระบบ',
        'default_level' => 1,
        'lockable'      => true,
    ],
    'connector_wizard' => [
        'name'          => 'Connector Wizard',
        'description'   => 'ตั้งค่าการเชื่อมต่อฐานข้อมูลและ mapping ของแต่ละระบบ',
        'group'         => 'ผู้ดูแลระบบ',
        'default_level' => 2,
        'lockable'      => true,
    ],
    'notifications' => [
        'name'          => 'Notification Channels',
        'description'   => 'จัดการช่องทางการแจ้งเตือน (Email / Webhook)',
        'group'         => 'ผู้ดูแลระบบ',
        'default_level' => 2,
        'lockable'      => true,
    ],
    'system_create_edit' => [
        'name'          => 'เพิ่ม/แก้ไขระบบที่เชื่อมต่อ',
        'description'   => 'สร้างและแก้ไขระบบ รวมถึง toggle 2-way sync',
        'group'         => 'ผู้ดูแลระบบ',
        'default_level' => 2,
        'lockable'      => true,
    ],

];
