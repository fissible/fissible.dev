---
title: Reference
description: ptyunit reference — @test, test_each, describe, PTY session API, custom assertions, @pty_skip, and runner flags.
---

## Basic tests

### @test

Define a test with a description string. Use the `run` helper to capture output and exit code.

```bash
@test "shows welcome message" {
    run my_program --help
    assert_output --partial "Usage:"
    assert_success
}
```

### run

Runs a command and captures its output and exit code into `$output` and `$status`.

```bash
run <command> [args...]
```

### Assertions

| Assertion | Passes when |
|-----------|-------------|
| `assert_success` | `$status` is 0 |
| `assert_failure` | `$status` is non-zero |
| `assert_output "<text>"` | `$output` equals the text exactly |
| `assert_output --partial "<text>"` | `$output` contains the text |

---

## test_each — parameterized tests

Run the same test body against multiple inputs using `@test_each`. The `%s` placeholder in the description is replaced with each value.

```bash
@test_each "handles input: %s" values=(foo bar baz) {
    run my_program "$value"
    assert_success
}
```

The current value is available as `$value` inside the test body.

---

## describe — scoped setup/teardown

Group related tests with `@describe`. Each block can define its own `setup` and `teardown` functions that run before and after every test in the block.

```bash
@describe "login flow" {
    setup() {
        start_server
    }

    teardown() {
        stop_server
    }

    @test "accepts valid credentials" {
        run login admin secret
        assert_success
    }

    @test "rejects invalid credentials" {
        run login admin wrong
        assert_failure
    }
}
```

`setup` and `teardown` in a `describe` block are scoped to that block and do not affect tests outside it.

---

## PTY session tests

Use the PTY driver to test interactive programs that require a real terminal.

### pty_run

Launches a command in a PTY session. Subsequent PTY helpers interact with this session.

```bash
pty_run <command> [args...]
```

### pty_send_key

Sends a keystroke to the running PTY session.

```bash
pty_send_key <key>   # e.g. DOWN, UP, ENTER, q
```

### pty_assert_output

Asserts that the PTY session's screen contains the given text.

```bash
pty_assert_output "<text>"
```

### Full PTY test example

```bash
@test "interactive menu navigation" {
    pty_run my_tui_program
    pty_send_key DOWN
    pty_send_key ENTER
    pty_assert_output "Option 2 selected"
}
```

---

## Custom assertions

Define custom assertion functions using `ptyunit_pass` and `ptyunit_fail`.

```bash
assert_json_field() {
    local field="$1" expected="$2"
    local actual
    actual=$(echo "$output" | jq -r ".$field")
    [ "$actual" = "$expected" ] \
        || ptyunit_fail "Expected $field=$expected, got $actual"
    ptyunit_pass
}
```

`ptyunit_fail` accepts a message and marks the test as failed. `ptyunit_pass` marks it as passed. Both are available inside any test body.

---

## @pty_skip

Annotate a test to skip it. The test is recorded in coverage output as skipped rather than omitted.

```bash
@pty_skip
@test "feature not yet implemented" {
    run my_program --experimental
    assert_success
}
```

---

## Runner flags

```bash
ptyunit [flags] <test_file_or_dir>
```

| Flag | Description |
|------|-------------|
| `--filter <pattern>` | Only run tests whose description matches the pattern |
| `--verbose` | Show output for passing tests as well as failing ones |
| `--bail` | Stop after the first failure |
| `--coverage` | Print a coverage summary including `@pty_skip` annotations |
