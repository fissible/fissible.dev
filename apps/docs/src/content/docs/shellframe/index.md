---
title: shellframe
description: TUI framework for bash — full widget set, composable runtime, 1000+ tests across bash 3.2–5.x.
---

shellframe is a TUI framework for bash. It provides a complete widget set, a composable
`shellframe_shell` runtime, split-pane layout, diff-view, and synchronized scrolling.
All modules are sourced — no binary dependencies.

## Quick start

```bash
brew install fissible/tap/shellframe
```

```bash
source shellframe.sh

shellframe_list_init items
shellframe_list_add items "Option A"
shellframe_list_add items "Option B"
shellframe_shell run
```

## Widgets

| Widget | Function prefix |
|--------|-----------------|
| List | `shellframe_list_*` |
| Grid | `shellframe_grid_*` |
| Editor | `shellframe_editor_*` |
| Tree | `shellframe_tree_*` |
| Modal | `shellframe_modal_*` |
| Tab bar | `shellframe_tabbar_*` |
| Input field | `shellframe_input_*` |
| App shell | `shellframe_shell_*` |

→ [Installation](installation) · [Reference](reference)
