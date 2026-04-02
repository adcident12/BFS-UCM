<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateOAuthKeys extends Command
{
    protected $signature   = 'oauth:generate-keys {--force : Overwrite existing keys}';
    protected $description = 'Generate the RSA-2048 key pair used to sign OAuth JWTs (RS256).';

    public function handle(): int
    {
        $dir         = storage_path('app/oauth');
        $privatePath = "{$dir}/private.pem";
        $publicPath  = "{$dir}/public.pem";

        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        if (file_exists($privatePath) && ! $this->option('force')) {
            $this->error('Keys already exist. Use --force to regenerate.');

            return self::FAILURE;
        }

        $this->info('Generating RSA-2048 key pair…');

        $res = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if (! $res) {
            $this->error('openssl_pkey_new() failed: '.openssl_error_string());

            return self::FAILURE;
        }

        openssl_pkey_export($res, $privateKey);
        $detail    = openssl_pkey_get_details($res);
        $publicKey = $detail['key'];

        file_put_contents($privatePath, $privateKey);
        file_put_contents($publicPath, $publicKey);
        chmod($privatePath, 0600);
        chmod($publicPath, 0644);

        $this->info("Private key → {$privatePath}");
        $this->info("Public key  → {$publicPath}");
        $this->newLine();
        $this->warn('Keep the private key secret. Do NOT commit it to version control.');

        return self::SUCCESS;
    }
}
