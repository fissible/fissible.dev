<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantLifecycleService
{
    public function suspend(Tenant $tenant): Tenant
    {
        return $this->updateStatus($tenant, 'suspended');
    }

    public function reactivate(Tenant $tenant): Tenant
    {
        return $this->updateStatus($tenant, 'active');
    }

    public function archive(Tenant $tenant): Tenant
    {
        return $this->updateStatus($tenant, 'archived');
    }

    public function restore(Tenant $tenant): Tenant
    {
        return $this->updateStatus($tenant, 'active');
    }

    public function convertDemoToPaid(Tenant $tenant): Tenant
    {
        $tenant->forceFill([
            'is_demo' => false,
            'demo_expires_at' => null,
            'status' => 'active',
        ])->save();

        return $tenant->refresh();
    }

    public function purge(Tenant $tenant): void
    {
        DB::transaction(function () use ($tenant): void {
            $tenant->delete();
        });
    }

    protected function updateStatus(Tenant $tenant, string $status): Tenant
    {
        $tenant->forceFill(['status' => $status])->save();

        return $tenant->refresh();
    }
}
