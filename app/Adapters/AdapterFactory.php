<?php

namespace App\Adapters;

use App\Models\System;
use InvalidArgumentException;

/**
 * สร้าง Adapter instance จาก System model
 *
 * เพิ่ม adapter ใหม่: ใส่ class ใน $map หรือเซ็ต adapter_class ใน systems table
 */
class AdapterFactory
{
    /** Map slug → Adapter class (fallback ถ้าไม่ได้เซ็ต adapter_class ใน DB) */
    protected static array $map = [
        'repair-system' => RepairSystemAdapter::class,
        'efiling'       => EFilingAdapter::class,
        // 'bfsapps'    => BfsappsAdapter::class,
        // 'pluto'      => PlutoAdapter::class,
    ];

    public static function make(System $system): SystemAdapterInterface
    {
        // ถ้า system กำหนด adapter_class ไว้ใน DB ให้ใช้นั้นก่อน
        $class = $system->adapter_class ?: (static::$map[$system->slug] ?? null);

        // Fallback: ถ้ามี ConnectorConfig ให้ใช้ DynamicAdapter
        if (! $class || ! class_exists($class)) {
            if (\App\Models\ConnectorConfig::where('system_id', $system->id)->exists()) {
                $class = DynamicAdapter::class;
            }
        }

        if (! $class || ! class_exists($class)) {
            throw new InvalidArgumentException(
                "ไม่พบ Adapter สำหรับระบบ '{$system->slug}' — กรุณาตั้งค่า adapter_class ใน System settings หรือใช้ Connector Wizard"
            );
        }

        return new $class($system);
    }

    public static function hasAdapter(System $system): bool
    {
        $class = $system->adapter_class ?: (static::$map[$system->slug] ?? null);

        if ($class && class_exists($class)) {
            return true;
        }

        // DynamicAdapter ถือว่ามี adapter ถ้ามี ConnectorConfig
        return \App\Models\ConnectorConfig::where('system_id', $system->id)->exists();
    }

    /**
     * คืน true เมื่อระบบ "เปิดใช้" 2-way permission sync
     *
     * ต้องผ่านทั้ง 2 เงื่อนไข:
     * 1. system->two_way_permissions = true (admin เปิด toggle)
     * 2. adapter รองรับ (supports2WayPermissions() = true)
     */
    public static function supports2WayPermissions(System $system): bool
    {
        if (! $system->two_way_permissions || ! static::hasAdapter($system)) {
            return false;
        }
        try {
            return static::make($system)->supports2WayPermissions();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * คืน true เมื่อ adapter รองรับ 2-way permission sync (capability check เท่านั้น)
     * ใช้ใน UI เพื่อแสดง toggle เฉพาะระบบที่ adapter รองรับ
     */
    public static function adapterSupports2Way(System $system): bool
    {
        if (! static::hasAdapter($system)) {
            return false;
        }
        try {
            return static::make($system)->supports2WayPermissions();
        } catch (\Throwable) {
            return false;
        }
    }
}
