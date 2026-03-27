---
title: Installation
description: Install ptyunit via Homebrew or from source. Requires bash 3.2+ and Python for the PTY driver.
---

## Requirements

- bash 3.2 or later
- Python (for `pty_run.py`, the PTY session driver) — Python 3 recommended
- No other external dependencies

ptyunit is tested across a Docker matrix covering bash 3.2, 4.4, 5.0, 5.1, and 5.2.

## Homebrew (recommended)

```bash
brew install fissible/tap/ptyunit
```

Verify the install:

```bash
ptyunit --version
```

## From source

```bash
git clone https://github.com/fissible/ptyunit.git
cd ptyunit
source ptyunit.sh
```

Add `ptyunit` to your `PATH` for use outside the repository:

```bash
export PATH="$PATH:/path/to/ptyunit/bin"
```

## Docker matrix

To run tests against all supported bash versions, use the Docker matrix:

```bash
# Run tests in bash 3.2
docker run --rm -v "$(pwd):/work" fissible/bash:3.2 ptyunit /work/tests/

# Or use the full matrix (3.2, 4.4, 5.0, 5.1, 5.2)
./run-matrix.sh
```

This is the same matrix used to validate ptyunit itself on every release.
