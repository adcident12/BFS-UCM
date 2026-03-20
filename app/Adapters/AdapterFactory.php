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

        if (! $class || ! class_exists($class)) {
            throw new InvalidArgumentException(
                "ไม่พบ Adapter สำหรับระบบ '{$system->slug}' — กรุณาตั้งค่า adapter_class ใน System settings"
            );
        }

        return new $class($system);
    }

    public static function hasAdapter(System $system): bool
    {
        $class = $system->adapter_class ?: (static::$map[$system->slug] ?? null);
        return $class && class_exists($class);
    }
}
