<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['station.domains.managed_root' => 'fissible.dev']);
    }

    public function test_tenant_homepage_renders(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Welcome to Acme',
            'slug' => 'home',
            'body' => '<p>Hello world</p>',
            'is_homepage' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('http://acme.fissible.dev/');

        $response->assertOk();
        $response->assertSee('Welcome to Acme');
        $response->assertSee('Hello world');
    }

    public function test_tenant_page_by_slug_renders(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'About Us',
            'slug' => 'about',
            'body' => '<p>About content</p>',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('http://acme.fissible.dev/about');

        $response->assertOk();
        $response->assertSee('About Us');
    }

    public function test_unpublished_page_returns_404(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get('http://acme.fissible.dev/draft');

        $response->assertNotFound();
    }

    public function test_future_published_page_returns_404(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'future',
            'published_at' => now()->addWeek(),
        ]);

        $response = $this->get('http://acme.fissible.dev/future');

        $response->assertNotFound();
    }

    public function test_body_is_sanitized(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'xss',
            'body' => '<p>Safe</p><script>alert("xss")</script>',
            'is_homepage' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('http://acme.fissible.dev/');

        $response->assertOk();
        $response->assertSee('Safe');
        $response->assertDontSee('<script>');
    }

    public function test_unknown_tenant_returns_404(): void
    {
        $response = $this->get('http://nonexistent.fissible.dev/');

        $response->assertNotFound();
    }
}
