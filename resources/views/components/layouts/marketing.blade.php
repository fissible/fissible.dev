<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'Self-hosted CMS and API platform with enforced approvals and contract validation.' }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>{{ isset($title) && $title !== 'fissible' ? $title . ' — fissible' : 'fissible' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Analytics (GA4) --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-G05EHVZYC7"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-G05EHVZYC7');
    </script>
</head>
<body>
    <x-announcement-bar />
    <x-nav />
    <main>
        {{ $slot }}
    </main>
    <x-footer />
</body>
</html>
