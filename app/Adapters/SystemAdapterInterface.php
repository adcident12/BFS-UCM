<?php

namespace App\Adapters;

use App\Models\UcmUser;

/**
 * Contract ที่ทุก System Adapter ต้องทำตาม
 *
 * แต่ละระบบเก่าจะมี Adapter ของตัวเองที่ implement interface นี้
 * UCM จะเรียกผ่าน interface นี้เสมอ ไม่ต้องรู้ว่าด้านในทำอะไร
 */
interface SystemAdapterInterface
{
    /**
     * Sync permissions ของ user ไปยังระบบเดิม
     *
     * @param  UcmUser  $user        ผู้ใช้ที่ต้องการ sync
     * @param  array    $permissions รายการ permission keys ที่ถูก grant เช่น ['role_admin', 'dept_1']
     * @return bool     true = สำเร็จ, false = ล้มเหลว
     */
    public function syncPermissions(UcmUser $user, array $permissions): bool;

    /**
     * ดึง permissions ทั้งหมดที่ระบบนี้รองรับ
     * ใช้ตอน seed permissions ครั้งแรก
     *
     * @return array  [['key' => 'role_admin', 'label' => 'ผู้ดูแลระบบ', 'group' => 'Role'], ...]
     */
    public function getAvailablePermissions(): array;

    /**
     * ดึง permissions ปัจจุบันของ user จากระบบเดิม
     * ใช้สำหรับ import สถานะเดิมเข้า UCM
     *
     * @param  UcmUser $user
     * @return array   permission keys ที่ user มีอยู่แล้ว
     */
    public function getCurrentPermissions(UcmUser $user): array;

    /**
     * สร้าง account ของ user ในระบบเดิม (ถ้ายังไม่มี)
     * UCM จะเรียก method นี้อัตโนมัติตอน sync แล้วไม่พบ user
     *
     * @param  UcmUser $user
     * @param  array   $permissions  permission keys ที่จะ assign พร้อมกับการสร้าง
     * @return bool    true = สร้างสำเร็จ, false = ไม่รองรับหรือล้มเหลว
     */
    public function createUser(UcmUser $user, array $permissions): bool;

    /**
     * ถอนสิทธิ์ทั้งหมดของ user ออกจากระบบเดิม
     */
    public function revokeAll(UcmUser $user): bool;

    /**
     * ดึงรายชื่อ users ทั้งหมดจาก remote system พร้อม flag ว่ามีใน UCM แล้วหรือไม่
     * @return array [['username'=>string, 'name'=>string, 'email'=>string,
     *                 'department'=>string, 'status'=>bool, 'in_ucm'=>bool], ...]
     */
    public function getSystemUsers(): array;

    /**
     * ค้นหา permission values ที่มีอยู่ใน remote system แต่ยังไม่มีใน UCM
     * → สร้าง system_permissions ใน UCM อัตโนมัติ
     * @return array  permission keys ที่สร้างใหม่
     */
    public function discoverPermissions(): array;

    /**
     * ดึงสถานะ account ของ user ในระบบเดิม
     * @return bool|null  true = active, false = disabled, null = ไม่มี account
     */
    public function getAccountStatus(UcmUser $user): ?bool;

    /**
     * เปิด/ปิด account ของ user ในระบบเดิม
     */
    public function setAccountStatus(UcmUser $user, bool $active): bool;

    /**
     * ทดสอบการเชื่อมต่อกับระบบเดิม
     * ใช้ใน UI แสดงสถานะ connection
     */
    public function testConnection(): array; // ['ok' => bool, 'message' => string]

    /**
     * สร้าง permission definition ในระบบภายนอก (UCM → External)
     *
     * เรียกอัตโนมัติเมื่อสร้าง permission ใน UCM และยังไม่มี remote_value
     * รองรับทั้ง auto-increment (คืน int/string ของ id ที่ได้)
     * และ string-based (คืน string ที่ใช้เป็น key ในระบบภายนอก)
     *
     * @return string|int|null  remote_value ที่จะเก็บใน UCM
     *                          null = ระบบนี้ไม่มี permission definition table (ไม่ต้อง provision)
     */
    public function provisionPermission(string $key, string $label, string $group): string|int|null;

    /**
     * อัปเดต permission definition ในระบบภายนอก (UCM → External)
     *
     * เรียกอัตโนมัติเมื่ออัปเดต label / group ของ permission ใน UCM และ 2-way เปิดอยู่
     * Adapter ที่ไม่มี definition table override คืน true เฉยๆ
     *
     * @param  string $remoteValue  ค่า remote_value ของ permission ที่จะอัปเดต
     * @param  string $label        label ใหม่
     * @param  string $group        group ใหม่
     * @return bool   true = อัปเดตสำเร็จหรือไม่จำเป็นต้องอัปเดต, false = เกิด error
     */
    public function updatePermission(string $remoteValue, string $label, string $group): bool;

    /**
     * ลบ permission definition ออกจากระบบภายนอก (UCM → External)
     *
     * เรียกอัตโนมัติเมื่อลบ permission ใน UCM
     * Adapter ที่ไม่มี definition table override คืน true เฉยๆ
     *
     * @param  string $remoteValue  ค่า remote_value ของ permission ที่จะลบ
     * @return bool   true = ลบสำเร็จหรือไม่จำเป็นต้องลบ, false = เกิด error
     */
    public function deletePermission(string $remoteValue): bool;

    /**
     * บอกว่า adapter นี้รองรับ 2-way permission sync จริงหรือไม่
     *
     * true = การเพิ่ม/ลบ permission ใน UCM จะมีผลต่อระบบภายนอกด้วย (provision + delete)
     * false = UCM เก็บข้อมูลเพื่อ sync user permissions เท่านั้น ไม่ได้จัดการ permission definitions
     *
     * ใช้ใน UI เพื่อแสดง badge "2-way" และ warning dialog ที่ถูกต้อง
     */
    public function supports2WayPermissions(): bool;

    /**
     * วิธีลบ permission definition ในระบบภายนอกเมื่อ admin ลบ permission ใน UCM
     *
     * Hard       = DELETE FROM definition table
     * Soft       = UPDATE definition table SET deleted_col = val
     * DetachOnly = ไม่แตะ definition table เลย (ลบเฉพาะใน UCM)
     */
    public function getPermissionDeleteMode(): \App\Enums\PermissionDeleteMode;

    // ── Managed Group CRUD ─────────────────────────────────────────────────
    // Adapter ที่มีตาราง reference (เช่น departments, document_categories) สามารถ
    // expose ตารางเหล่านั้นให้ UCM จัดการ CRUD ได้โดยตรง
    // Adapter ที่ไม่รองรับให้ BaseAdapter default คืนค่าเปล่า

    /**
     * ชื่อ group ที่ adapter ต้องการให้ UCM จัดการ CRUD โดยตรง
     * @return string[]  เช่น ['Department', 'Document Category'] หรือ []
     */
    public function getManagedGroups(): array;

    /**
     * ดึง records ทั้งหมดของ group ที่ระบุ
     * @return array  [['id' => int, 'name' => string], ...]
     */
    public function getGroupRecords(string $group): array;

    /**
     * Schema ของ fields เพิ่มเติมสำหรับ add/edit records ในกลุ่มนี้
     * (นอกจาก 'name' ที่มีอยู่เสมอ)
     * @return array  ['field' => ['label'=>string, 'type'=>'text|number', 'required'=>bool, ...], ...]
     */
    public function getGroupSchema(string $group): array;

    /**
     * เพิ่ม record ใหม่ในกลุ่มที่ระบุ
     * @param  array $extra  extra fields จาก getGroupSchema (เช่น priority, filename)
     * @return array|false  ['id' => int, 'name' => string, ...] หรือ false ถ้าล้มเหลว
     */
    public function addGroupRecord(string $group, string $name, array $extra = []): array|false;

    /**
     * อัปเดต record (ใช้ id เป็น identifier)
     * @param  array $extra  extra fields จาก getGroupSchema
     */
    public function updateGroupRecord(string $group, int $id, string $name, array $extra = []): bool;

    /**
     * ลบ record ออกจากกลุ่มที่ระบุ
     */
    public function deleteGroupRecord(string $group, int $id): bool;

    /**
     * วิธีลบ record ของกลุ่มที่ระบุ: 'hard' หรือ 'soft'
     */
    public function getGroupDeleteMode(string $group): string;
}
