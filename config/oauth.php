<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RSA Key Paths
    |--------------------------------------------------------------------------
    | Paths to the RS256 private/public key pair used to sign and verify JWTs.
    | Generate via: php artisan oauth:generate-keys
    */
    'private_key' => storage_path('app/oauth/private.pem'),
    'public_key'  => storage_path('app/oauth/public.pem'),

    /*
    |--------------------------------------------------------------------------
    | Token TTLs (seconds)
    |--------------------------------------------------------------------------
    */
    'access_token_ttl'  => env('OAUTH_ACCESS_TOKEN_TTL', 3600),     // 1 hour
    'refresh_token_ttl' => env('OAUTH_REFRESH_TOKEN_TTL', 2592000), // 30 days
    'auth_code_ttl'     => env('OAUTH_AUTH_CODE_TTL', 600),         // 10 minutes

    /*
    |--------------------------------------------------------------------------
    | JWKS Key ID
    |--------------------------------------------------------------------------
    */
    'kid' => env('OAUTH_KID', 'ucm-rs256-1'),

];
