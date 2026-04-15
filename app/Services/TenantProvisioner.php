<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantMembership;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantProvisioner
{
    public function __construct(
        protected TenantDomainService $domains,
        protected TenantSeeder $seeder,
    ) {
    }

    /**
     * @param  array{name:string,slug:string,is_demo?:bool,demo_expires_at?:Carbon|null}  $attributes
     * @param  array{owner_email?:string|null,owner_name?:string|null,owner_password?:string|null,added_via?:string,seed_demo?:bool,allow_reserved_slug?:bool}  $options
     */
    public function provision(array $attributes, array $options = []): Tenant
    {
        return DB::transaction(function () use ($attributes, $options): Tenant {
            $slug = $this->domains->ensureAllowedSlug(
                $attributes['slug'],
                (bool) ($options['allow_reserved_slug'] ?? false)
            );

            $tenant = Tenant::create([
                'uuid' => (string) Str::uuid(),
                'name' => trim($attributes['name']),
                'slug' => $slug,
                'status' => 'active',
                'is_demo' => (bool) ($attributes['is_demo'] ?? false),
                'demo_expires_at' => $attributes['demo_expires_at'] ?? null,
            ]);

            $owner = $this->resolveOwner($options);

            if ($owner) {
                TenantMembership::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'user_id' => $owner->id,
                    ],
                    [
                        'role' => 'admin',
                        'status' => 'active',
                        'added_via' => $options['added_via'] ?? 'platform',
                    ]
                );
            }

            if (($options['seed_demo'] ?? true) === true) {
                $this->seeder->seedDemoContent($tenant, $owner);
            }

            return $tenant->refresh();
        });
    }

    protected function resolveOwner(array $options): ?User
    {
        $email = $options['owner_email'] ?? null;

        if (! is_string($email) || trim($email) === '') {
            return null;
        }

        $email = trim(strtolower($email));

        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $options['owner_name'] ?? Str::before($email, '@'),
                'password' => $options['owner_password'] ?? Str::random(32),
            ]
        );
    }
}
