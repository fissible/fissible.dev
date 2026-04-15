<?php

use App\Http\Controllers\TenantSiteController;
use Illuminate\Support\Facades\Route;

Route::domain('{tenantSlug}.' . config('station.domains.managed_root'))
    ->middleware(['web', 'tenant.resolve'])
    ->group(function () {
        Route::get('/', [TenantSiteController::class, 'home']);
        Route::get('/{slug}', [TenantSiteController::class, 'page'])
            ->where('slug', '[a-z0-9\-]+');
    });
