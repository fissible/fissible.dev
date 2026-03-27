---
title: Installation
description: Install accord via Composer and configure the middleware.
---

```bash
composer require fissible/accord
```

Publish the config:
```bash
php artisan vendor:publish --tag=accord-config
```

This creates `config/accord.php`:
```php
return [
    'spec'               => base_path('openapi.yaml'),
    'validate_requests'  => true,
    'validate_responses' => true,
];
```

← [Overview](../) · [Reference](reference) →
