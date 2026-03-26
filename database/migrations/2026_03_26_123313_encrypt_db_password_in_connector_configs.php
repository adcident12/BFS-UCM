<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Re-encrypt existing plaintext db_password values.
 *
 * ConnectorConfig now uses the `encrypted` cast for db_password.
 * This migration converts any rows that still have plaintext passwords
 * into encrypted ciphertext so the cast can decrypt them correctly.
 *
 * Detection: Laravel encrypted values always start with "eyJ" (base64 of JSON).
 * If the stored value does NOT start with that prefix we treat it as plaintext
 * and re-save it via the model (which will trigger the cast).
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('connector_configs')
            ->whereNotNull('db_password')
            ->where('db_password', '!=', '')
            ->get(['id', 'db_password']);

        foreach ($rows as $row) {
            // Laravel Crypt::encryptString() output is base64-encoded JSON → starts with "eyJ"
            if (str_starts_with((string) $row->db_password, 'eyJ')) {
                continue; // Already encrypted — skip
            }

            try {
                DB::table('connector_configs')
                    ->where('id', $row->id)
                    ->update(['db_password' => Crypt::encryptString($row->db_password)]);
            } catch (\Throwable $e) {
                Log::error("[Migration] Failed to encrypt db_password for connector_config #{$row->id}: ".$e->getMessage());
            }
        }
    }

    public function down(): void
    {
        // Intentionally a no-op:
        // Decrypting back to plaintext would defeat the purpose of this migration.
        // To roll back: remove the `encrypted` cast from ConnectorConfig and
        // manually update each password via ConnectorConfig::find($id)->update([...]).
    }
};
