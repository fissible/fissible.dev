---
title: Reference
description: accord middleware, config, and validation error format.
---

## Config

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `spec` | `string` | `base_path('openapi.yaml')` | Path to OpenAPI spec |
| `validate_requests` | `bool` | `true` | Validate incoming requests |
| `validate_responses` | `bool` | `true` | Validate outgoing responses |

## Validation errors

When validation fails, accord returns a `422 Unprocessable Entity` with:

```json
{
  "message": "Request validation failed",
  "errors": [
    { "pointer": "/body/email", "message": "Value must be a valid email address" }
  ]
}
```

← [Installation](installation)
