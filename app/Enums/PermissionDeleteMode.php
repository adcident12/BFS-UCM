<?php

namespace App\Enums;

enum PermissionDeleteMode: string
{
    case Hard = 'hard';
    case Soft = 'soft';
    case DetachOnly = 'detach_only';

    public function label(): string
    {
        return match ($this) {
            self::Hard       => 'Hard Delete — ลบถาวรจาก DB ระบบปลายทาง',
            self::Soft       => 'Soft Delete — ทำเครื่องหมายลบ ไม่ลบจริง',
            self::DetachOnly => 'Detach Only — ลบเฉพาะใน UCM ไม่แตะระบบปลายทาง',
        };
    }
}
