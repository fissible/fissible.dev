<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantMenu;
use App\Models\TenantPage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TenantSeeder
{
    public function seedDemoContent(Tenant $tenant, ?User $demoUser = null): void
    {
        $now = Carbon::now();

        if ($demoUser && $tenant->slug === config('station.demo.shared_slug')) {
            $demoUser->forceFill([
                'name' => config('station.demo.user_name'),
                'email' => config('station.demo.user_email'),
                'password' => Hash::make(config('station.demo.user_password')),
            ])->save();
        }

        $tenant->pages()->delete();
        $tenant->menus()->delete();

        TenantPage::create([
            'tenant_id' => $tenant->id,
            'slug' => 'home',
            'title' => "{$tenant->name} Home",
            'excerpt' => 'Approval-first publishing for teams that cannot afford mistakes.',
            'body' => implode("\n\n", [
                "# {$tenant->name}",
                'This demo tenant shows the kind of structured marketing and policy content a Station site can ship out of the box.',
                'Highlights:',
                '- homepage content that feels intentional instead of empty',
                '- editorial controls and approval-first publishing posture',
                '- tenant isolation with reusable platform operations',
            ]),
            'is_homepage' => true,
            'published_at' => $now,
        ]);

        TenantPage::create([
            'tenant_id' => $tenant->id,
            'slug' => 'privacy',
            'title' => 'Privacy Policy',
            'excerpt' => 'A seeded policy page so the demo site has realistic supporting content.',
            'body' => implode("\n\n", [
                '# Privacy Policy',
                'This is seeded demo content intended to show a realistic supporting page.',
                'Replace this with your actual policy content before going live.',
            ]),
            'is_system' => true,
            'published_at' => $now,
        ]);

        TenantPage::create([
            'tenant_id' => $tenant->id,
            'slug' => 'contact',
            'title' => 'Contact',
            'excerpt' => 'A seeded contact page with a placeholder form block.',
            'body' => implode("\n\n", [
                '# Contact',
                'This demo page represents a contact page managed inside Station.',
                'Placeholder form fields:',
                '- Name',
                '- Email',
                '- Message',
            ]),
            'published_at' => $now,
        ]);

        TenantMenu::create([
            'tenant_id' => $tenant->id,
            'location' => 'primary',
            'label' => 'Primary Menu',
            'items' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Contact', 'url' => '/contact'],
            ],
        ]);

        TenantMenu::create([
            'tenant_id' => $tenant->id,
            'location' => 'secondary',
            'label' => 'Secondary Menu',
            'items' => [
                ['label' => 'Privacy', 'url' => '/privacy'],
            ],
        ]);

        $tenant->forceFill([
            'settings' => [
                'demo_user_email' => $demoUser?->email,
                'seeded_at' => $now->toIso8601String(),
                'homepage_slug' => 'home',
            ],
        ])->save();
    }
}
