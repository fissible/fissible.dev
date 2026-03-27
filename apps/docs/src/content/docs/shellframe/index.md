---
title: shellframe
description: TUI framework for bash — full widget set, composable runtime, split-pane layout, diff-view, synchronized scrolling. Tested across bash 3.2–5.x with no binary dependencies.
---

shellframe is a TUI framework for bash. It provides a complete widget set, a composable runtime, split-pane layout, diff-view, and synchronized scrolling. All modules are sourced — no binary dependencies. Tested across bash 3.2, 4.4, 5.0, 5.1, and 5.2.

## Two abstraction levels

shellframe offers two ways to build terminal UIs:

**1. Direct widget use** — import individual widget modules and render widgets yourself. Full control over layout and event handling.

**2. `shellframe_app` runtime** — an FSM-driven application runtime. Define screens with `enter`, `render`, and `input` hooks, then call `shellframe_app run`. The runtime handles the event loop, screen transitions, and input dispatch.

## Widgets

| Widget | Function prefix | Status |
|--------|-----------------|--------|
| List | `shellframe_list_*` | v1 (stable) |
| Grid | `shellframe_grid_*` | v1 (stable) |
| Editor | `shellframe_editor_*` | v1 (stable) |
| Tree | `shellframe_tree_*` | v1 (stable) |
| Modal | `shellframe_modal_*` | v1 (stable) |
| Tab bar | `shellframe_tabbar_*` | v1 (stable) |
| Input field | `shellframe_input_*` | v1 (stable) |
| App shell | `shellframe_shell_*` | v1 (stable) |
| Split pane | `shellframe_split_*` | v2 (preview) |
| Diff view | `shellframe_diff_*` | v2 (preview) |

v2 widgets are functional but their APIs may change before a stable release.

## Quick start

Install shellframe and build a minimal single-screen application using `shellframe_app`:

```bash
brew install fissible/tap/shellframe
```

```bash
source shellframe.sh

# Register a screen with enter/render/input hooks
shellframe_app_register home \
    enter='home_enter' \
    render='home_render' \
    input='home_input'

home_enter() {
    shellframe_list_init items
    shellframe_list_add items "Option A"
    shellframe_list_add items "Option B"
    shellframe_list_add items "Option C"
}

home_render() {
    shellframe_list_render items
}

home_input() {
    case "$1" in
        q) shellframe_app exit ;;
    esac
}

# Start the application at the 'home' screen
shellframe_app run home
```

Press `q` to exit. The `shellframe_app run` call starts the event loop and blocks until the app exits.

## Modules

shellframe is organized into three layers:

- **`shellframe.sh`** — main entry point; sources all modules
- **`src/widgets/`** — individual widget implementations
- **`src/runtime/`** — `shellframe_shell` composable runtime and event loop
- **`src/app/`** — `shellframe_app` FSM driver
