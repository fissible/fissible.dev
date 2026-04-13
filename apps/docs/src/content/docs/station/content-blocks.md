---
title: Content Blocks
description: Block-based content editing system using Filament Builder, a PHP registry, and JSON storage within existing entry data.
sidebar:
  order: 11
---

Content blocks provide a block-based editing experience for building rich page layouts. Instead of a single rich text field, editors compose content from discrete blocks — text, images, callouts, embeds, and more — each with its own settings and frontend rendering.

## Using content blocks

To use blocks in a content type, add a field with type `content_blocks`. In the entry editor, this renders as a block editor with drag-and-drop reordering, collapsible sections, and per-block configuration forms.

![Content block editor showing Embed, Pull Quote, and Text blocks with theme and width settings](/station/content-blocks-editor.png)

Each block has two shared settings:
- **Theme** — `light`, `dark`, or `brand`
- **Width** — `full`, `contained` (max 720px), or `narrow` (max 540px)

Content types can restrict which blocks are available using the `allowed_blocks` option. At most one `content_blocks` field is allowed per content type.

## Available block types

| Block | Type key | Description |
|-------|----------|-------------|
| **Text** | `text` | Rich text content |
| **Image** | `image` | Image with alt text and optional caption |
| **Callout** | `callout` | Highlighted message or tip |
| **Pull Quote** | `pull_quote` | Standalone quotation |
| **Embed** | `embed` | External content (video, widget, code) |
| **CTA** | `cta` | Call-to-action with heading, text, and button |
| **Form** | `form` | Embedded form from the [Forms](/station/forms/) module |

Each block defines its own form fields in the admin and renders via a dedicated Blade view on the frontend.

## Restricting blocks per content type

Content types can limit which blocks are available:

```json
{
  "name": "content_blocks",
  "label": "Content",
  "type": "content_blocks",
  "allowed_blocks": ["text", "image", "callout"],
  "max_blocks": 20
}
```

When `allowed_blocks` is omitted, all registered block types are available. The `max_blocks` option caps how many blocks an editor can add (default: 50).

## How blocks are stored

Blocks are stored as JSON in the entry's `data` column — no additional tables or columns. Each block records its type and data:

```json
{
  "content_blocks": [
    {
      "type": "text",
      "data": {
        "body": "Hello world",
        "theme": "light",
        "width": "contained"
      }
    },
    {
      "type": "image",
      "data": {
        "image_url": "https://example.com/photo.jpg",
        "alt": "Sunset",
        "caption": "Photo by...",
        "theme": "dark",
        "width": "full"
      }
    }
  ]
}
```

## EntryResource integration

`EntryResource::buildContentFields()` handles the `content_blocks` field type:

```php
'content_blocks' => Builder::make($name)
    ->label($label)
    ->blocks(
        collect(app(ContentBlockRegistry::class)->forContentType($contentType))
            ->map(fn (string $class) => $class::toFilamentBlock())
            ->toArray()
    )
    ->collapsible()
    ->cloneable()
    ->maxItems($field['max_blocks'] ?? 50)
    ->columnSpan('full'),
```

The block editor provides drag-and-drop reordering, add/remove controls, JSON serialization, and per-block form schemas out of the box.

## Content type rules

- Maximum one `content_blocks` field per content type
- Field name and type cannot be changed after creation
- Changing `required` requires confirmation
- UI shows affected entries when destructive changes are made

## Frontend rendering

`Entry::displayBody()` resolves content in priority order:

1. `content_blocks` field (if present) — each block rendered via its Blade view with wrapper
2. First non-empty `rich_text` or `textarea` field
3. Empty string if none found

Unknown block types are skipped and logged. Each block renders through `renderWithWrapper()`:

```html
<div class="cb cb--text cb-theme--light cb-width--contained">
    {!! $data['body'] !!}
</div>
```

## CSS classes

Block classes follow a consistent naming convention:

```css
/* Block types */
.cb { }
.cb--text { }
.cb--image { }
.cb--callout { }
.cb--pull-quote { }
.cb--embed { }
.cb--cta { }

/* Themes */
.cb-theme--light { }
.cb-theme--dark { }
.cb-theme--brand { }

/* Widths */
.cb-width--full { }
.cb-width--contained { max-width: 720px; margin: 0 auto; }
.cb-width--narrow { max-width: 540px; margin: 0 auto; }
```

PHP uses underscores (`pull_quote`), CSS uses hyphens (`cb--pull-quote`). Themes define the final visual styling.

## Review mode and diffs

- **Current (v1):** the blocks array is treated as a single field. `DiffService` shows "Content blocks changed."
- **Planned:** per-block diff support showing individual additions, removals, and edits.

## Building custom blocks

Custom blocks extend the `ContentBlock` abstract class and register with the `ContentBlockRegistry`:

```php
use App\Cms\Blocks\ContentBlock;
use App\Cms\Blocks\ContentBlockRegistry;

// In a service provider:
app(ContentBlockRegistry::class)->register('custom', CustomBlock::class);
```

The `ContentBlock` class requires these static methods:

| Method | Returns | Purpose |
|--------|---------|---------|
| `type()` | string | Block type key |
| `label()` | string | Display name in admin |
| `icon()` | string | Heroicon name |
| `schema()` | array | Filament form fields |
| `view()` | string | Blade view path |

Optional methods:
- `normalize(array $data)` — migrate data from older schemas
- `renderWithWrapper(array $data)` — render HTML with theme/width wrapper

## Limitations and future work

- **Media manager integration** — image blocks currently use URLs; Spatie MediaLibrary integration is deferred
- **Block-level diffs** — planned for better review mode visibility
- **Advanced block logic** — conditional rendering and nested blocks are deferred
