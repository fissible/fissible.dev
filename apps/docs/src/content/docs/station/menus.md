---
title: Menus
description: Named menus with nested items, drag-and-drop ordering, driver-based item types, and role-based visibility.
---

Station's menu builder provides named, hierarchical menus with configurable item types and role-based visibility. Menus are tenant-scoped and managed through a drag-and-drop admin interface.

## Menu model

Each menu has:

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Display name (e.g., "Main Navigation") |
| `handle` | string | Unique identifier per tenant (e.g., `primary`) |
| `max_depth` | integer | Maximum nesting level (default: 4) |

### Protected menus

The `primary` and `secondary` menu handles are protected and cannot be deleted. They are created during installation.

## Menu items

Each item belongs to a menu and has:

| Field | Type | Description |
|-------|------|-------------|
| `label` | string | Display text |
| `type` | string | Driver key (see item types below) |
| `parent_id` | FK (nullable) | Parent item for nesting |
| `sort_order` | integer | Position among siblings |
| `required_role_level` | integer (nullable) | Minimum role level for visibility |
| `config` | JSON | Driver-specific configuration |

Items form a tree via `parent_id` self-references. The system enforces depth limits against the menu's `max_depth` and prevents cycles.

## Item type drivers

Menu items use a **driver pattern** — each type key maps to a class implementing `MenuItemTypeDriver`. The driver defines how the item resolves its URL and what configuration fields it needs.

### External

Manual URL link.

- `url` — the target URL
- `target` — `_self` (default) or `_blank`

### Entry

Links to a specific published entry. The URL is resolved dynamically from the entry's route.

- `entry_id` — the linked entry

If the entry is unpublished or deleted, the item is excluded from rendering.

### Listing

Links to a content type's listing page.

- `content_type_id` — the linked content type

Resolves to `/{route_pattern}` for the selected content type.

### Placeholder (expanding)

Generates multiple child nodes at render time. Does not link to a URL itself. Three modes:

| Mode | Behavior | Config |
|------|----------|--------|
| `pages` | Expands to the full page tree | — |
| `pages_from` | Expands to a subtree starting from a specific page | `slug` — root page slug |
| `listing` | Expands to the N most recent entries from a content type | `content_type_id`, `limit` (default varies) |

Placeholder items are resolved by the `MenuRenderer` at render time. The expanded nodes inherit the placeholder's nesting position.

## Role-based visibility

Each item has an optional `required_role_level` field:

| Level | Role |
|-------|------|
| 1 | Reviewer |
| 2 | Author |
| 3 | Editor |
| 4 | Admin |
| 5 | Super Admin |

When set, the item (and its children) is only visible to users with that role level or higher. A null value means the item is visible to everyone, including unauthenticated visitors.

Filtering happens at render time in the `MenuRenderer` service.

## Admin UI

Menu management uses a **DraggableTree** Livewire component powered by SortableJS:

- **Drag-and-drop** reordering and nesting
- **Inline edit panel** — clicking an item opens a side panel with driver-specific fields
- **Add item** — select a driver type, configure, and place in the tree
- **Delete** — removes the item and re-parents or removes children

The driver-specific fields in the edit panel change dynamically based on the item type. For example, an Entry item shows an entry search field, while an External item shows URL and target inputs.

## Frontend rendering

The `MenuRenderer` service resolves a menu for display:

```php
$nodes = app(MenuRenderer::class)->render('primary', $viewer);
```

**Parameters:**
- `handle` — the menu handle to resolve
- `$viewer` — the current user (or null for guests), used for role filtering

**Returns** an array of node trees. Each node contains:
- `label`, `url`, `target`
- `children` — nested array of child nodes
- `is_active` — whether the node matches the current URL

In Blade templates:

```blade
@foreach ($menu as $node)
    <a href="{{ $node['url'] }}" target="{{ $node['target'] ?? '_self' }}"
       @class(['active' => $node['is_active']])>
        {{ $node['label'] }}
    </a>
    @if (!empty($node['children']))
        {{-- Render children recursively --}}
    @endif
@endforeach
```

## Custom drivers

Register custom item type drivers via `MenuItemTypeRegistry` in a service provider:

```php
use App\Cms\Menus\MenuItemTypeRegistry;

public function boot(): void
{
    app(MenuItemTypeRegistry::class)->register('custom', CustomDriver::class);
}
```

The driver class must implement `MenuItemTypeDriver`, which requires:

- `label()` — display name for the admin UI
- `configSchema()` — Filament form fields for configuration
- `resolve(MenuItem $item)` — returns the resolved URL (or null to hide the item)
