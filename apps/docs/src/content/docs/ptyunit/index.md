---
title: ptyunit
description: PTY test framework for bash — 221 assertions, 15× faster than bats-core.
---

ptyunit is a standalone PTY test framework for bash. It runs your program in a real
pseudo-terminal and makes assertions against its output and behavior.

```bash
brew install fissible/tap/ptyunit
```

```bash
source ptyunit.sh

pty_test "shows welcome screen" <<'EOF'
  pty_run "shql"
  pty_assert_contains "Welcome"
EOF
```

→ [Installation](installation) · [Reference](reference)
