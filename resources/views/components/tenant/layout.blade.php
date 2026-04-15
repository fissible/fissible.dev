@props(['tenant', 'page', 'menus'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} — {{ $tenant->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <nav class="nav">
        <a href="/" class="nav-logo">{{ $tenant->name }}</a>
        @if(isset($menus['primary']) && $menus['primary']->first())
            <div class="nav-links">
                @foreach($menus['primary']->first()->items ?? [] as $label => $url)
                    <a href="{{ $url }}" class="nav-link">{{ $label }}</a>
                @endforeach
            </div>
        @endif
    </nav>
    <main>
        {{ $slot }}
    </main>
</body>
</html>
