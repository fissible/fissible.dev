---
title: Reference
description: watch reference — middleware aliases, CSRF and HTMX, extending the cockpit, and Blade component namespace.
---

## Middleware aliases

watch registers two middleware aliases you can use in your application:

| Alias | Behaviour |
|-------|-----------|
| `watch.writable` | Returns 403 when `WATCH_WRITABLE` is not `true` |
| `watch.local` | Returns 403 when the app environment is not `local`, `development`, or `testing` |

Use `watch.local` on the watch route group to prevent the cockpit from being accessible in staging or production:

```php
Route::middleware(['watch.local'])->group(function () {
    // watch routes
});
```

Use `watch.writable` on individual cockpit actions that modify files:

```php
Route::post('/watch/forge/generate', GenerateSpecController::class)
    ->middleware('watch.writable');
```

---

## CSRF and HTMX

watch uses HTMX for server round trips. HTMX sends a `HX-Request` header with every request. watch's routes are not on the `web` middleware group, so they do not use Laravel's CSRF cookie by default.

If you wrap watch routes in a `web`-stack middleware group, configure HTMX to send the CSRF token:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    document.body.addEventListener('htmx:configRequest', (event) => {
        event.detail.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
    });
</script>
```

---

## Extending the cockpit

Any package can add pages to the watch cockpit by following three steps:

**1. Register routes under the `watch.*` naming convention:**

```php
Route::get('/watch/my-feature', MyFeatureController::class)
    ->name('watch.my-feature');
```

**2. Use the `<x-watch::layout>` Blade component:**

```blade
<x-watch::layout title="My Feature">
    <!-- your page content -->
</x-watch::layout>
```

**3. Nav link appears automatically:**

watch checks for registered routes via `Route::has('watch.my-feature')`. When the route exists, a nav link appears in the cockpit sidebar automatically — no additional registration needed.

---

## Blade component namespace

watch publishes Blade components under the `watch::` namespace:

| Component | Description |
|-----------|-------------|
| `<x-watch::layout>` | Full cockpit page layout with nav and header |

Use `<x-watch::layout>` in any Blade view that should appear inside the cockpit shell.
