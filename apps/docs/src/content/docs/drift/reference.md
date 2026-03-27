---
title: Reference
description: drift Artisan commands and change categories.
---

## Artisan commands

```bash
# Suggest the next version bump
php artisan accord:version

# Generate a changelog from the diff between two specs
php artisan drift:changelog --from openapi-v1.yaml --to openapi.yaml

# Show coverage of your routes by the spec
php artisan drift:coverage
```

## Change categories

| Category | Semver impact |
|----------|---------------|
| Removed endpoint | Major |
| Changed required parameter | Major |
| Added required parameter | Major |
| Added optional parameter | Minor |
| Added endpoint | Minor |
| Changed response field type | Major |
| Added response field | Minor |

← [Installation](installation)
