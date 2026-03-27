---
title: forge
description: OpenAPI spec generation from Laravel routes. Reads routes and FormRequest validation rules, produces valid OpenAPI 3.0 YAML. The artisan command is accord:generate.
---

forge generates an OpenAPI 3.0 spec from your existing Laravel routes. It inspects route definitions and FormRequest validation rules and produces a valid OpenAPI 3.0 YAML file — giving you a spec to validate against with [accord](../accord/) without writing it by hand.

The artisan command is `accord:generate` (forge registers under the accord namespace).

```bash
composer require fissible/forge
php artisan accord:generate
```

## Recommended workflow

1. Run `php artisan accord:generate` to scaffold the spec from your existing routes
2. Fill in response schemas in the generated YAML (forge infers request schemas from FormRequests; response schemas require manual input)
3. Commit the spec to your repository
4. Validate requests and responses at runtime with [accord](../accord/)
5. Detect drift over time with [drift](../drift/)

## Quick start

```bash
# Install
composer require fissible/forge

# Generate spec for v1 routes (output: resources/openapi/v1.yaml)
php artisan accord:generate

# Generate for a different version
php artisan accord:generate --version=v2

# Customize title and output path
php artisan accord:generate --title="Acme API" --output=docs/openapi.yaml

# Overwrite an existing spec
php artisan accord:generate --force
```

The generated spec is immediately usable with accord. Commit it, then add accord middleware and let validation begin.
