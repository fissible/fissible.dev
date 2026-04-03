# Blog Post Outline: "Why I built a SQLite workbench in bash"

**Post on:** dev.to
**Target length:** 800–1200 words
**Link targets:** /tools/shellql, https://github.com/fissible/shellql

---

## Hook

You SSH into a server. The SQLite database is right there — you can see it in the filesystem.

Every GUI tool you own stops working. TablePlus, DB Browser, Beekeeper — all of them need a local connection. sqlite3 is available, but it's raw SQL with no browsing. litecli is read-biased and still needs installing.

You need to look at some rows, update a field, check an index. You end up writing SELECT statements into a CLI, copying output into a notes file, writing UPDATE statements by hand.

There's a better way.

---

## The build

ShellQL is built on shellframe — a TUI framework I wrote in bash. shellframe handles screen management, keyboard routing, dirty-region rendering, and component lifecycle. Writing a new application on top of it is closer to writing a React app than writing a bash script.

The surprising parts:
- Mouse support in bash is real, and it's not that hard once you understand xterm escape sequences
- SQLite's `.schema` output is parseable enough to build a schema browser without any external tools
- Tab management (multiple tables open simultaneously) required rethinking shellframe's focus model

---

## The SSH use case

This is the thing that makes ShellQL different from every other SQLite tool.

If the machine has bash and sqlite3, ShellQL runs. That means:
- Production servers (read and write, with care)
- Docker containers
- CI environments for debugging test databases
- Remote dev boxes

No GUI install. No port forwarding. No pulling the file to your laptop and pushing it back.

SSH in, run `shql /var/app/production.db`, browse your data.

---

## Full CRUD

Most TUI database tools are read-only. ShellQL isn't.

The record editor is a schema-aware form overlay. It shows column types and NOT NULL constraints. Tab through fields, edit values, press Enter to submit. Insert new rows the same way.

Table creation uses a SQL query tab preloaded with a `CREATE TABLE` template — you get full DDL control without a rigid GUI wizard.

---

## Mouse support

This one surprised people in early demos. Most bash tools are keyboard-only by design. ShellQL supports both.

Keyboard navigation is fast once you learn it — the keybindings are shown at the bottom of every screen. Mouse works for everything else: clicking into tables, scrolling rows, selecting records.

This matters for adoption. Not everyone who SSHes into a server is a power user.

---

## Install and try it

```bash
brew install fissible/tap/shellql
shql my.db
```

- Tool page: https://fissible.dev/tools/shellql
- GitHub: https://github.com/fissible/shellql
