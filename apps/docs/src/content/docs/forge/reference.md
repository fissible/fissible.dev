---
title: Reference
description: forge reference — accord:generate command options, schema inference from Laravel validation rules, and example output.
---

## accord:generate

```bash
php artisan accord:generate [options]
```

### Options

| Option | Default | Description |
|--------|---------|-------------|
| `--version` | `v1` | URI version filter — only include routes matching `/v{N}/` |
| `--title` | `API` | Sets `info.title` in the generated spec |
| `--output` | `resources/openapi/{version}.yaml` | Output file path |
| `--force` | — | Overwrite an existing file without prompting |

### Examples

```bash
# Generate spec for v1 routes (default)
php artisan accord:generate

# Generate spec for v2 routes
php artisan accord:generate --version=v2

# Custom title and output path
php artisan accord:generate --title="Acme API" --output=docs/openapi.yaml

# Overwrite existing spec
php artisan accord:generate --force
```

---

## Schema inference

forge reads your FormRequest `rules()` method and maps Laravel validation rules to JSON Schema properties.

| Validation rule | JSON Schema effect |
|----------------|--------------------|
| `integer`, `int`, `numeric` | `type: integer` |
| `boolean`, `bool` | `type: boolean` |
| `array` | `type: array` |
| `string` (default) | `type: string` |
| `email` | `format: email` |
| `url` | `format: uri` |
| `date` | `format: date` |
| `uuid` | `format: uuid` |
| `nullable` | `nullable: true` |
| `min:N` | `minLength` / `minimum` (type-dependent) |
| `max:N` | `maxLength` / `maximum` (type-dependent) |
| `in:a,b,c` | `enum: [a, b, c]` |
| `required` | Field added to `required` array |

Fields not matched by any type rule default to `type: string`.

---

## Example output

Given a route `POST /v1/users` with a `StoreUserRequest` containing:

```php
public function rules(): array
{
    return [
        'name'  => ['required', 'string', 'max:255'],
        'email' => ['required', 'email'],
        'age'   => ['nullable', 'integer', 'min:0'],
    ];
}
```

forge generates:

```yaml
openapi: 3.0.3
info:
  title: API
  version: 1.0.0
paths:
  /v1/users:
    post:
      summary: Store User
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required:
                - name
                - email
              properties:
                name:
                  type: string
                  maxLength: 255
                email:
                  type: string
                  format: email
                age:
                  type: integer
                  minimum: 0
                  nullable: true
      responses:
        '200':
          description: OK
```

Response schemas are scaffolded as stubs. Fill them in manually after generation.

---

## FormRequest inspector

forge discovers FormRequest classes by inspecting the route's controller method signature. It resolves the FormRequest class and calls `rules()` to extract validation rules.

Routes without a FormRequest (or using `Request` directly) will have no request body schema in the generated spec — they are included in the spec with an empty request body.
