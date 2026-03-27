---
title: Installation
description: Install watch and register the service provider.
---

```bash
composer require fissible/watch
```

Register in `config/app.php`:
```php
'providers' => [
    // ...
    Fissible\Watch\WatchServiceProvider::class,
],
```

Visit `/watch` in your browser.

← [Overview](../) · [Reference](reference) →
