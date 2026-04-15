<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantProvisioner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'platform@fissible.dev'],
            [
                'name' => 'Platform Admin',
                'password' => Str::random(32),
            ]
        );

        if (! Tenant::query()->where('slug', config('station.demo.shared_slug'))->exists()) {
            app(TenantProvisioner::class)->provision([
                'name' => config('station.demo.shared_name'),
                'slug' => config('station.demo.shared_slug'),
                'is_demo' => true,
                'demo_expires_at' => now()->addDays(config('station.demo.lifetime_days')),
            ], [
                'owner_name' => config('station.demo.user_name'),
                'owner_email' => config('station.demo.user_email'),
                'owner_password' => config('station.demo.user_password'),
                'added_via' => 'platform',
                'seed_demo' => true,
                'allow_reserved_slug' => true,
            ]);
        }
    }
}
