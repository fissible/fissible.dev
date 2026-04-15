<x-tenant.layout :tenant="$tenant" :page="$page" :menus="$menus">
    <article class="section">
        <div class="section-inner">
            <h1>{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="marketing-tagline">{{ $page->excerpt }}</p>
            @endif
            <div class="tenant-body">
                {!! $body !!}
            </div>
        </div>
    </article>
</x-tenant.layout>
