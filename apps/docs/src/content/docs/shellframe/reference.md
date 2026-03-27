---
title: Reference
description: shellframe API reference — shellframe_app FSM driver, shellframe_shell runtime, and all widget function prefixes.
---

## shellframe_app

The FSM-driven application runtime. Define screens with `enter`, `render`, and `input` hooks. The runtime manages the event loop, screen transitions, and input dispatch.

### shellframe_app_register

Register a screen by name. Provide callback function names for each hook.

```bash
shellframe_app_register <screen_name> \
    enter='<function_name>' \
    render='<function_name>' \
    input='<function_name>'
```

- **`enter`** — called once when the screen becomes active. Use to initialize state.
- **`render`** — called each frame. Call widget render functions here.
- **`input`** — called with a single argument: the key pressed. Handle navigation and actions.

### shellframe_app run

Start the application at the named screen. Blocks until the app exits.

```bash
shellframe_app run <screen_name>
```

### shellframe_app exit

Exit the application from within an `input` hook.

```bash
shellframe_app exit
```

### shellframe_app goto

Transition to a different registered screen.

```bash
shellframe_app goto <screen_name>
```

---

## shellframe_shell runtime

The composable runtime. Use this for simpler applications that don't need FSM-style screen management.

### shellframe_shell run

Starts the event loop. Blocks until the shell exits.

```bash
shellframe_shell run
```

### shellframe_shell_mark_dirty

Marks the app shell for re-render on the next frame.

```bash
shellframe_shell_mark_dirty
```

---

## Widgets

### List — `shellframe_list_*` (v1, stable)

A scrollable, selectable list.

```bash
shellframe_list_init <varname>
shellframe_list_add  <varname> <item>
shellframe_list_render <varname>
```

### Grid — `shellframe_grid_*` (v1, stable)

Tabular data display in a grid layout.

```bash
shellframe_grid_init   <varname>
shellframe_grid_render <varname>
```

### Editor — `shellframe_editor_*` (v1, stable)

Multi-line text editor widget.

```bash
shellframe_editor_init   <varname>
shellframe_editor_render <varname>
```

### Tree — `shellframe_tree_*` (v1, stable)

Hierarchical tree navigation widget.

```bash
shellframe_tree_init   <varname>
shellframe_tree_render <varname>
```

### Modal — `shellframe_modal_*` (v1, stable)

Overlay dialog widget.

```bash
shellframe_modal_init   <varname>
shellframe_modal_render <varname>
```

### Tab bar — `shellframe_tabbar_*` (v1, stable)

Horizontal tab navigation bar.

```bash
shellframe_tabbar_init   <varname>
shellframe_tabbar_render <varname>
```

### Input field — `shellframe_input_*` (v1, stable)

Single-line text input widget.

```bash
shellframe_input_init   <varname>
shellframe_input_render <varname>
```

### App shell — `shellframe_shell_*` (v1, stable)

The composable runtime shell. See [shellframe_shell runtime](#shellframe_shell-runtime) above.

---

## v2 Preview widgets

The following widgets are functional but their APIs may change before a stable release.

### Split pane — `shellframe_split_*` (v2, preview)

Splits the terminal into two panes, either side-by-side or stacked.

```bash
shellframe_split_init <varname> <direction>   # direction: h (horizontal) | v (vertical)
shellframe_split_render <varname>
```

`h` produces a left/right layout; `v` produces a top/bottom layout.

### Diff view — `shellframe_diff_*` (v2, preview)

Side-by-side diff display with synchronized scrolling.

```bash
shellframe_diff_init   <varname>
shellframe_diff_render <varname>
```
