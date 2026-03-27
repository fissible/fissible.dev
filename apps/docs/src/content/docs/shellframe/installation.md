---
title: Installation
description: Install shellframe via Homebrew or from source. Requires bash 3.2+ with no external dependencies.
---

## Requirements

- bash 3.2 or later (macOS ships with bash 3.2 by default; Linux typically ships 5.x)
- No external dependencies — all modules are sourced bash scripts with no binary requirements

shellframe is tested across a Docker matrix covering bash 3.2, 4.4, 5.0, 5.1, and 5.2.

## Homebrew (recommended)

```bash
brew install fissible/tap/shellframe
```

After installation, `shellframe.sh` is available on your `PATH`. Source it from any script:

```bash
source shellframe.sh
```

## From source

Clone the repository and source the main entry point directly:

```bash
git clone https://github.com/fissible/shellframe.git
cd shellframe
source shellframe.sh
```

To use shellframe from scripts located outside the cloned directory, use an absolute path:

```bash
source /path/to/shellframe/shellframe.sh
```

## Verifying the install

After sourcing `shellframe.sh`, the widget functions are available in the current shell session:

```bash
source shellframe.sh
type shellframe_list_init
# shellframe_list_init is a function
```
