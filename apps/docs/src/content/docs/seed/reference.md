---
title: Reference
description: seed generator reference — all 31 generators and output formats.
---

## Usage

```bash
seed <generator> [--count N] [--format json|csv|sql|kv]
```

## Scalar generators

| Generator | Example output |
|-----------|----------------|
| `scalar.uuid` | `550e8400-e29b-41d4-a716-446655440000` |
| `scalar.name` | `Alice Nguyen` |
| `scalar.email` | `alice@example.com` |
| `scalar.phone` | `+1-555-0147` |
| `scalar.date` | `2024-03-15` |
| `scalar.bool` | `true` |
| `scalar.int` | `42` |
| `scalar.float` | `3.14` |
| `scalar.word` | `clarity` |
| `scalar.sentence` | `The quick brown fox.` |

## Record generators

| Generator | Fields |
|-----------|--------|
| `record.user` | uuid, name, email, created_at |
| `record.address` | street, city, state, zip, country |
| `record.company` | name, domain, industry |

## Output formats

| Flag | Output |
|------|--------|
| `--format json` | JSON array |
| `--format csv` | CSV with header row |
| `--format sql` | INSERT statements |
| `--format kv` | KEY=value lines |

← [Installation](installation)
