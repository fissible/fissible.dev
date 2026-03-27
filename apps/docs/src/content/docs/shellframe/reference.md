---
title: Reference
description: shellframe API reference — widget functions, app shell, and events.
---

## App Shell

### shellframe_shell run

Starts the event loop. Blocks until the app exits.

```bash
shellframe_shell run
```

### shellframe_shell_mark_dirty

Marks the app shell for re-render on the next frame.

```bash
shellframe_shell_mark_dirty
```

## List widget

### shellframe_list_init

```bash
shellframe_list_init <varname>
```

Initialises a list store. Call before any other `shellframe_list_*` functions.

### shellframe_list_add

```bash
shellframe_list_add <varname> <item>
```

Appends an item to the list.

### shellframe_list_render

```bash
shellframe_list_render <varname>
```

Renders the list to the current frame buffer.

## Split pane

### shellframe_split_init

```bash
shellframe_split_init <varname> <direction>  # direction: h | v
```

Initialises a split-pane layout. `h` splits horizontally (left/right); `v` splits vertically (top/bottom).

← [Installation](installation)
