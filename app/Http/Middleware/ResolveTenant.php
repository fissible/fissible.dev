<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if ($this->shouldSkip($host)) {
            return $next($request);
        }

        $managedRoot = config('station.domains.managed_root');
        $subdomain = str_replace('.' . $managedRoot, '', $host);

        if ($subdomain === $host || $subdomain === '') {
            return $next($request);
        }

        $tenant = Tenant::where('slug', $subdomain)
            ->where('status', 'active')
            ->first();

        abort_unless($tenant, 404);

        app()->instance('tenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }

    protected function shouldSkip(string $host): bool
    {
        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return true;
        }

        $managedRoot = config('station.domains.managed_root');

        if ($host === $managedRoot || $host === 'www.' . $managedRoot) {
            return true;
        }

        $appHosts = config('station.domains.app_hosts', []);

        return in_array($host, $appHosts, true);
    }
}
