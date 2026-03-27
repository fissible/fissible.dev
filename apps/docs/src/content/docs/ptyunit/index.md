---
title: ptyunit
description: PTY test framework for bash — test interactive terminal programs by scripting real PTY sessions. Runs on bash 3.2–5.x.
---

ptyunit is a PTY test framework for bash. It lets you test interactive terminal programs — including TUI applications — by scripting real pseudo-terminal sessions. Tests run against the actual program, not a mock.

```bash
brew install fissible/tap/ptyunit
```

## Why ptyunit

Most bash test frameworks (bats-core, shellspec, shunit2) can only test commands that read from stdin and write to stdout in a simple pipe. They cannot handle programs that:

- Use raw mode or alternate screen buffers
- Read keyboard input directly from the TTY
- Render full-screen TUI interfaces

ptyunit solves this with a PTY driver (`pty_run.py`) that spawns a real pseudo-terminal session. You send keystrokes, advance the session, and assert against the rendered output — just like a real user would.

## Quick start

```bash
source ptyunit.sh

@test "shows welcome message" {
    run my_program --help
    assert_output --partial "Usage:"
    assert_success
}
```

Run your test file:

```bash
ptyunit my_test.sh
```

## PTY session test

For interactive programs, use the PTY helpers:

```bash
@test "interactive menu navigation" {
    pty_run my_tui_program
    pty_send_key DOWN
    pty_send_key ENTER
    pty_assert_output "Option 2 selected"
}
```

## Key features

- **PTY driver** (`pty_run.py`) — spawns real PTY sessions for interactive program testing
- **Mocking system** — stub commands or functions during test execution
- **`test_each`** — parameterized tests from arrays
- **`describe` blocks** — scoped `setup`/`teardown` per block
- **`run` helper** — captures exit code and output
- **Custom assertions** — via `ptyunit_pass` / `ptyunit_fail`
- **Coverage tracking** — with `@pty_skip` annotation
- **Docker matrix** — tested across bash 3.2, 4.4, 5.0, 5.1, 5.2
