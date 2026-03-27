---
title: Installation
description: Install shellql from source or via macbin. Requires bash 3.2+, sqlite3, and shellframe.
---

## Requirements

- bash 3.2 or later
- sqlite3 — pre-installed on macOS; install with `apt install sqlite3` on Debian/Ubuntu
- shellframe — required; installed automatically as a dependency when using Homebrew or macbin

## From source

Clone the repository and run the install script:

```bash
git clone https://github.com/fissible/shellql.git
cd shellql
./install.sh
```

The installer places the `shql` binary on your `PATH` and bundles shellframe alongside it.

## macbin

shellql is available via fissible/macbin:

```bash
macbin install shellql
```

## Verifying the install

```bash
shql --version
```

Open a test database:

```bash
shql /path/to/any.db
```

## Bundled shellframe

shellql bundles a compatible version of shellframe. You do not need a separate shellframe installation to use shellql, but if you develop shellframe-based apps yourself, be aware that shellql ships its own copy.
