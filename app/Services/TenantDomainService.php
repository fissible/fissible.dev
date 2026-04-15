<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TenantDomainService
{
    public function normalizeSlug(string $slug): string
    {
        return Str::of($slug)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9\-]+/', '-')
            ->replaceMatches('/-+/', '-')
            ->trim('-')
            ->toString();
    }

    public function ensureAllowedSlug(string $slug, bool $allowReserved = false): string
    {
        $normalized = $this->normalizeSlug($slug);

        if ($normalized === '') {
            throw new InvalidArgumentException('A tenant slug is required.');
        }

        if (! $allowReserved && in_array($normalized, config('station.domains.reserved_slugs', []), true)) {
            throw new InvalidArgumentException('That tenant slug is reserved.');
        }

        return $normalized;
    }

    public function managedHostname(Tenant $tenant): string
    {
        return "{$tenant->slug}.".config('station.domains.managed_root');
    }
}
