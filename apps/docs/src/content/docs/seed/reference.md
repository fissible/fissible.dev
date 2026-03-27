---
title: Reference
description: seed generator reference — all 37 generators, output format flags, --seed, --locale, schema wizard, and MCP server.
---

## Usage

```bash
seed <generator> [count] [flags]
```

`count` is an optional integer. Without it, seed outputs one value.

## Generators

### Names

| Generator | Example output |
|-----------|----------------|
| `first` | `Alice` |
| `last` | `Nguyen` |
| `name` | `Alice Nguyen` |
| `username` | `alice_nguyen42` |

### Internet

| Generator | Example output |
|-----------|----------------|
| `email` | `alice@example.com` |
| `domain` | `example.com` |
| `url` | `https://example.com/path` |
| `ip` | `192.168.1.42` |
| `ipv6` | `2001:db8::1` |
| `mac` | `00:1A:2B:3C:4D:5E` |
| `slug` | `quick-brown-fox` |

### Address

| Generator | Example output |
|-----------|----------------|
| `street` | `123 Maple St` |
| `city` | `Austin` |
| `state` | `TX` |
| `zip` | `78701` |
| `country` | `United States` |
| `phone` | `+1-555-0147` |

### Company

| Generator | Example output |
|-----------|----------------|
| `company` | `Acme Corp` |
| `department` | `Engineering` |
| `jobtitle` | `Senior Software Engineer` |

### Numbers

| Generator | Example output |
|-----------|----------------|
| `integer` | `42` |
| `float` | `3.14` |
| `decimal` | `9.99` |
| `sequence` | `1`, `2`, `3` … |

### Text

| Generator | Example output |
|-----------|----------------|
| `word` | `clarity` |
| `sentence` | `The quick brown fox.` |
| `paragraph` | multi-sentence text block |
| `uuid` | `550e8400-e29b-41d4-a716-446655440000` |
| `color` | `coral` |
| `hex` | `#f4a261` |

### Date/Time

| Generator | Example output |
|-----------|----------------|
| `date` | `2024-03-15` |
| `time` | `14:32:00` |
| `datetime` | `2024-03-15T14:32:00` |
| `timestamp` | `1710510720` |

### File

| Generator | Example output |
|-----------|----------------|
| `filename` | `report_final.pdf` |
| `extension` | `pdf` |
| `mimetype` | `application/pdf` |
| `filepath` | `/home/user/documents/report.pdf` |

---

## Flags

| Flag | Description |
|------|-------------|
| `--plain` | Plain text output, one value per line (default) |
| `--json` | JSON array output |
| `--csv` | CSV with header row |
| `--tsv` | TSV (tab-separated values) |
| `--seed=N` | Set random seed for reproducible output |
| `--locale=XX_XX` | Locale for localized generators (e.g. `de_DE`, `fr_FR`) |

---

## Schema wizard

`seed new` launches an interactive schema builder:

```bash
seed new
```

The wizard prompts for field names and generator types, then saves a `seed_schema.json` file. Run a saved schema with:

```bash
seed run seed_schema.json
```

Schemas produce multi-field structured records. The `seed_cart` feature generates nested JSON for complex record types.

---

## MCP server

Start the MCP server:

```bash
seed mcp
```

Claude Desktop configuration (`~/.config/claude/claude_desktop_config.json`):

```json
{
  "mcpServers": {
    "seed": {
      "command": "seed",
      "args": ["mcp"]
    }
  }
}
```

Once configured, all 37 generators are available as MCP tools inside Claude Desktop and any other MCP-compatible client.
