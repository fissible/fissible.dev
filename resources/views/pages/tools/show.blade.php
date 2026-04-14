<x-layouts.marketing :title="$package['name']" :description="$package['tagline']">
    @if ($package['slug'] === 'shellql')
        <x-shellql-page :package="$package" />
    @else
        <x-marketing-page :package="$package" />
    @endif
</x-layouts.marketing>
