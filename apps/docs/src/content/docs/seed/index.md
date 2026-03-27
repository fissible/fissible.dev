---
title: seed
description: Bash fake data generator — 31 generators, 4 output formats, MCP server.
---

seed is a bash fake data generator. No runtime, no package manager — bash and awk only.

```bash
brew install fissible/tap/seed
```

## Generators

seed provides 31 generators across 5 categories: scalar, record, ecommerce, CRM, and TUI.

```bash
# List all generators
seed --list

# Generate data
seed record.user --count 5 --format json
```

→ [Installation](installation) · [Reference](reference)
