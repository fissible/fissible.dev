<?php

namespace App\Http\Controllers;

use App\Models\TenantPage;
use Illuminate\Http\Request;

class TenantSiteController extends Controller
{
    public function home(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $page = TenantPage::where('tenant_id', $tenant->id)
            ->where('is_homepage', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        abort_unless($page, 404);

        return view('tenant.page', [
            'tenant' => $tenant,
            'page' => $page,
            'body' => $this->sanitizeHtml($page->body),
            'menus' => $tenant->menus->groupBy('location'),
        ]);
    }

    public function page(Request $request, string $tenantSlug, string $slug)
    {
        $tenant = $request->attributes->get('tenant');

        $page = TenantPage::where('tenant_id', $tenant->id)
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        abort_unless($page, 404);

        return view('tenant.page', [
            'tenant' => $tenant,
            'page' => $page,
            'body' => $this->sanitizeHtml($page->body),
            'menus' => $tenant->menus->groupBy('location'),
        ]);
    }

    protected function sanitizeHtml(string $html): string
    {
        return strip_tags(
            $html,
            '<p><br><h1><h2><h3><h4><h5><h6><ul><ol><li><a><strong><em><blockquote><code><pre><img><table><thead><tbody><tr><th><td>'
        );
    }
}
