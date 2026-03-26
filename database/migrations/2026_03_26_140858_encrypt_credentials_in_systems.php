<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('systems')
            ->where(function ($q) {
                $q->whereNotNull('db_password')->orWhereNotNull('api_token');
            })
            ->get(['id', 'db_password', 'api_token']);

        foreach ($rows as $row) {
            $updates = [];

            if ($row->db_password !== null && ! $this->isEncrypted($row->db_password)) {
                $updates['db_password'] = Crypt::encryptString($row->db_password);
            }

            if ($row->api_token !== null && ! $this->isEncrypted($row->api_token)) {
                $updates['api_token'] = Crypt::encryptString($row->api_token);
            }

            if ($updates) {
                DB::table('systems')->where('id', $row->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        // ไม่สามารถ reverse ได้ — ต้อง restore จาก backup
    }

    /** Laravel encrypted values เริ่มต้นด้วย "eyJ" (base64-encoded JSON) */
    private function isEncrypted(string $value): bool
    {
        return str_starts_with($value, 'eyJ');
    }
};
