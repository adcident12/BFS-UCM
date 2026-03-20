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
}
