---
title: Reference
description: shellql CLI reference — all invocation modes, flags, themes, and source architecture.
---

## CLI

```bash
shql <database-file> [table] [flags]
shql databases
```

### Invocation modes

| Invocation | Behaviour |
|------------|-----------|
| `shql my.db` | Open in interactive mode (schema browser) |
| `shql my.db <table>` | Open directly to the named table |
| `shql my.db --query` | Open directly in REPL/query mode |
| `shql my.db -q "<SQL>"` | Run SQL, print output, and exit |
| `cat query.sql \| shql my.db` | Read SQL from stdin, run it, and exit |
| `shql databases` | Discovery mode — list known/recent databases |

### Flags

| Flag | Description |
|------|-------------|
| `-q "<SQL>"`, `--query "<SQL>"` | Execute a SQL query and exit immediately |
| `--query` (no argument) | Open in REPL/query mode |

### Environment variables

| Variable | Description |
|----------|-------------|
| `SHQL_THEME` | Set the UI theme. Values: `basic` (default), `uranium` |

---

## Themes

| Theme | Header style | Border style | Value colour |
|-------|-------------|--------------|--------------|
| `basic` | Reverse-video | Single-line | Default |
| `uranium` | Neon green | Rounded | Cyan |

```bash
SHQL_THEME=uranium shql my.db
```

---

## Architecture

shellql is a shellframe application. Its source is organized as:

```
shql                  CLI entry point
└── src/
    ├── cli.sh        Argument parsing and mode dispatch
    ├── connections.sh Connection registry and sigil aggregation
    ├── db.sh         sqlite3 adapter
    ├── screens/
    │   ├── welcome.sh
    │   ├── schema.sh
    │   ├── table.sh
    │   ├── query.sh
    │   └── record.sh
    └── state.sh      Application globals
```

Each file in `src/screens/` defines a shellframe_app screen with `enter`, `render`, and `input` hooks. The CLI dispatches to the appropriate screen based on the arguments passed to `shql`.
