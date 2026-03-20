<?php

namespace App\Providers;

use App\Auth\LdapUserProvider;
use App\Services\LdapService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LdapService::class);
    }

    public function boot(): void
    {
        Auth::provider('ldap', function ($app) {
            return new LdapUserProvider($app->make(LdapService::class));
        });
    }
}
