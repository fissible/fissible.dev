---
title: Reference
description: accord reference — failure modes, config, spec sources, middleware registration, testing trait, and version extraction.
---

## Failure modes

| Mode | Behaviour |
|------|-----------|
| `exception` | Throws `ContractViolationException` (default) |
| `log` | Logs a `warning` via PSR-3; request continues |
| `callable` | Calls your callable with `ValidationResult`; request continues |

Set via the `ACCORD_FAILURE_MODE` environment variable or in `config/accord.php`.

**Callable example:**

```php
// config/accord.php
'failure_mode'     => 'callable',
'failure_callable' => function (\Fissible\Accord\ValidationResult $result): void {
    logger()->warning('Contract violation', ['result' => $result->toArray()]);
},
```

---

## Configuration

`config/accord.php`:

```php
return [
    'failure_mode'     => env('ACCORD_FAILURE_MODE', 'exception'),
    'failure_callable' => null,
    'version_pattern'  => '/^\/v(\d+)(?:\/|$)/',
    'spec_source'      => env('ACCORD_SPEC_SOURCE', 'file'),
    'spec_pattern'     => env('ACCORD_SPEC_PATTERN', '{base}/resources/openapi/{version}'),
    'spec_cache_ttl'   => env('ACCORD_SPEC_CACHE_TTL', 3600),
];
```

| Key | Default | Description |
|-----|---------|-------------|
| `failure_mode` | `exception` | How to handle violations: `exception`, `log`, or `callable` |
| `failure_callable` | `null` | Callable used when `failure_mode` is `callable` |
| `version_pattern` | `/^\/v(\d+)(?:\/|$)/` | Regex to extract the API version from the URI |
| `spec_source` | `file` | Spec source type: `file` or `url` |
| `spec_pattern` | `{base}/resources/openapi/{version}` | Path template for spec files |
| `spec_cache_ttl` | `3600` | Cache TTL in seconds for remote specs |

---

## Version extraction

accord extracts the API version from the URI using `version_pattern`:

| URI | Extracted version |
|-----|------------------|
| `/v1/users` | `v1` |
| `/v2/orders/123` | `v2` |
| `/api/v3/items` | `v3` |
| `/users` | *(no version — validation skipped)* |

---

## Spec sources

### FileSpecSource (default)

Loads spec files from the local filesystem. The path is resolved using `spec_pattern` with `{base}` replaced by `base_path()` and `{version}` replaced by the extracted version.

```dotenv
ACCORD_SPEC_SOURCE=file
ACCORD_SPEC_PATTERN={base}/resources/openapi/{version}
```

### UrlSpecSource

Loads the spec from a remote URL. Caches the response using a PSR-16 cache implementation.

```dotenv
ACCORD_SPEC_SOURCE=url
ACCORD_SPEC_PATTERN=https://api.example.com/openapi/{version}.yaml
ACCORD_SPEC_CACHE_TTL=3600
```

### Custom spec source

Implement `SpecSourceInterface`:

```php
use Fissible\Accord\Contracts\SpecSourceInterface;

class MySpecSource implements SpecSourceInterface
{
    public function load(string $version): array
    {
        // Return parsed spec as a PHP array
    }
}
```

Bind it in a service provider:

```php
$this->app->bind(SpecSourceInterface::class, MySpecSource::class);
```

---

## Testing trait

Use `AssertsApiContracts` in your Laravel feature tests to assert that responses conform to the spec:

```php
use Fissible\Accord\Drivers\Laravel\Testing\AssertsApiContracts;

class UserApiTest extends TestCase
{
    use RefreshDatabase, AssertsApiContracts;

    public function test_index_matches_contract(): void
    {
        $response = $this->getJson('/v1/users');
        $response->assertOk();
        $this->assertResponseMatchesContract($response);
    }
}
```

`assertResponseMatchesContract` validates the response against the spec for the request URI and HTTP method. The test fails if there is a schema violation.
