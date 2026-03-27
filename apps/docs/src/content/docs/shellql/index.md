---
title: shellql
description: Terminal SQLite workbench built on shellframe — interactive schema browser, table view, query screen, record inspector, and discovery mode.
---

shellql (`shql`) is a terminal SQLite workbench built on shellframe. Open databases interactively, run queries, browse tables, and inspect records — all from the terminal, all keyboard-driven.

## Usage modes

```bash
shql my.db                                    # interactive mode — opens schema browser
shql my.db -q "SELECT * FROM users LIMIT 10" # quick query and exit
shql my.db --query                            # open directly in REPL/query mode
shql my.db users                              # open directly to table view
cat query.sql | shql my.db                    # pipe a SQL file
shql databases                                # discovery mode — list known/recent databases
```

## Quick start

```bash
brew install fissible/tap/shellql
shql myapp.db
```

shellql opens to the schema browser. Navigate with arrow keys, press Enter to open a table, and press `/` to switch to query mode.

## Themes

```bash
SHQL_THEME=uranium shql my.db   # neon green header, rounded borders, cyan values
SHQL_THEME=basic   shql my.db   # default: reverse-video header, single borders
```

| Theme | Description |
|-------|-------------|
| `basic` | Default. Reverse-video header, single-line borders. |
| `uranium` | Neon green header, rounded borders, cyan values. |

Set the theme permanently by exporting `SHQL_THEME` in your shell profile.

## Screens

| Screen | How to reach it |
|--------|----------------|
| Schema browser | Default on open |
| Table view | Select a table from schema browser; or `shql my.db <table>` |
| Query / REPL | Press `/` from any screen; or `shql my.db --query` |
| Record inspector | Select a row from table view |
| Welcome | Shown before a database is loaded |
