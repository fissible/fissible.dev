<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolveTenant;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function resolveFromHost(string $host): mixed
    {
        config(['station.domains.managed_root' => 'fissible.dev']);

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_HOST' => $host,
        ]);

        $middleware = new ResolveTenant;
        $result = null;

        $middleware->handle($request, function ($req) use (&$result) {
            $result = $req->attributes->get('tenant');
            return response('ok');
        });

        return $result;
    }

    public function test_resolves_active_tenant_from_subdomain(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'acme',
            'status' => 'active',
        ]);

        $resolved = $this->resolveFromHost('acme.fissible.dev');

        $this->assertNotNull($resolved);
        $this->assertEquals($tenant->id, $resolved->id);
    }

    public function test_skips_root_domain(): void
    {
        $resolved = $this->resolveFromHost('fissible.dev');
        $this->assertNull($resolved);
    }

    public function test_skips_www(): void
    {
        $resolved = $this->resolveFromHost('www.fissible.dev');
        $this->assertNull($resolved);
    }

    public function test_skips_app_hosts(): void
    {
        config(['station.domains.app_hosts' => ['platform.fissible.dev']]);

        $resolved = $this->resolveFromHost('platform.fissible.dev');
        $this->assertNull($resolved);
    }

    public function test_skips_localhost(): void
    {
        $resolved = $this->resolveFromHost('localhost');
        $this->assertNull($resolved);
    }

    public function test_404s_for_unknown_slug(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->resolveFromHost('nonexistent.fissible.dev');
    }

    public function test_404s_for_suspended_tenant(): void
    {
        Tenant::factory()->create([
            'slug' => 'suspended-co',
            'status' => 'suspended',
        ]);

        $this->expectException(NotFoundHttpException::class);
        $this->resolveFromHost('suspended-co.fissible.dev');
    }
}
