<?php

namespace App\Providers;

use App\Services\TenantDomainService;
use App\Services\TenantLifecycleService;
use App\Services\TenantProvisioner;
use App\Services\TenantSeeder;
use Illuminate\Support\ServiceProvider;

class StationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantDomainService::class);
        $this->app->singleton(TenantLifecycleService::class);
        $this->app->singleton(TenantSeeder::class);
        $this->app->singleton(TenantProvisioner::class);
    }

    public function boot(): void
    {
        //
    }
}
