---
title: seed
description: Bash fake data generator — 37 generators across 8 categories, 4 output formats, MCP server, and schema wizard. No runtime dependencies beyond bash.
---

seed is a bash fake data generator. It provides 37 generators across 8 categories with four output formats: plain text, JSON, CSV, and TSV. No runtime, no package manager — bash only.

```bash
brew install fissible/tap/seed
```

## Generator categories

| Category | Generators |
|----------|-----------|
| Names | `first`, `last`, `name`, `username` |
| Internet | `email`, `domain`, `url`, `ip`, `ipv6`, `mac`, `slug` |
| Address | `street`, `city`, `state`, `zip`, `country`, `phone` |
| Company | `company`, `department`, `jobtitle` |
| Numbers | `integer`, `float`, `decimal`, `sequence` |
| Text | `word`, `sentence`, `paragraph`, `uuid`, `color`, `hex` |
| Date/Time | `date`, `time`, `datetime`, `timestamp` |
| File | `filename`, `extension`, `mimetype`, `filepath` |

## Quick start

```bash
# Single value
seed name

# Multiple values
seed name 5

# JSON output
seed email --json

# CSV with multiple records
seed email 10 --csv

# Reproducible output
seed date --seed=42

# Locale support
seed name --locale=de_DE
```

## Output formats

```bash
seed name 3           # plain (default) — one value per line
seed name 3 --json    # JSON array
seed name 3 --csv     # CSV with header row
seed name 3 --tsv     # TSV
```

## Schema wizard

The `seed new` command launches an interactive schema builder that saves a reusable `seed_schema.json`:

```bash
seed new              # interactive schema builder
seed run schema.json  # run a saved schema
```

Schemas produce structured multi-field records. The `seed_cart` feature generates nested JSON structures for multi-field records.

## MCP server

seed ships with an MCP server, making all generators available to Claude and other MCP-compatible AI tools:

```bash
seed mcp   # start the MCP server
```

See the [Reference](reference) page for MCP configuration details.
