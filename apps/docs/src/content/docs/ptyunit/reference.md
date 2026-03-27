---
title: Reference
description: ptyunit assertion and runner reference.
---

## pty_test

```bash
pty_test "<description>" <<'EOF'
  # test body
EOF
```

Defines a test. The heredoc body runs in a subshell with ptyunit functions in scope.

## pty_run

```bash
pty_run "<command>"
```

Launches `<command>` in a PTY and captures its output.

## Assertions

| Assertion | Passes when |
|-----------|-------------|
| `pty_assert_contains "<text>"` | output contains the string |
| `pty_assert_not_contains "<text>"` | output does not contain the string |
| `pty_assert_exit_code <n>` | program exited with code `n` |
| `pty_assert_line_count <n>` | output has exactly `n` lines |

← [Installation](installation)
