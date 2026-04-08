---
title: Content Blocks
description: Block-based content editing system using Filament Builder, a PHP registry, and JSON storage within existing entry data.
---

The Content Blocks system provides a block-based content editing experience within Station's dynamic fields framework. It uses Filament's Builder component and a PHP `ContentBlockRegistry` to define reusable block types.

Blocks are stored as JSON in the `entries.data` column — no additional migrations, tables, or columns required.

## Architecture

The system has three components:

1. **Field type** — a `content_blocks` field in the content type's fields array, rendered via Filament Builder
2. **Block registry** — `ContentBlockRegistry` maps block type keys to PHP classes
3. **Block contract** — `ContentBlock` abstract class defines schema, rendering, and base fields for each block

### Storage format

Blocks are saved as JSON in `entries.data`:

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

## Block registry

`ContentBlockRegistry` manages available block types:

- `register(type, class)` — register a block type
- `resolve(type)` — resolve a block class from its type string
- `forContentType(contentType)` — filter blocks to those allowed by a content type

Content types can restrict which blocks are available using an allowlist:

```json
{
  "name": "content_blocks",
  "label": "Content",
  "type": "content_blocks",
  "allowed_blocks": ["text", "image", "callout"]
}
```

## Block contract

The `ContentBlock` abstract class defines all block behavior through static methods (blocks are stateless):

**Base fields** included on every block:
- `theme` — `light`, `dark`, or `brand`
- `width` — `full`, `contained`, or `narrow`

**Methods:**
- `type()`, `label()`, `icon()` — block identity
- `schema()` — Filament form schema for the block's fields
- `view()` — Blade view path for frontend rendering
- `toFilamentBlock()` — returns a Filament `Builder\Block` instance
- `renderWithWrapper(array $data)` — renders HTML with theme/width wrapper
- `normalize(array $data)` — optional schema migrations for backwards compatibility

## Starter block types

| Block | Type key | View | Icon |
|-------|----------|------|------|
| Text | `text` | `blocks.text` | `heroicon-o-document-text` |
| Image | `image` | `blocks.image` | `heroicon-o-photo` |
| Callout | `callout` | `blocks.callout` | `heroicon-o-megaphone` |
| Pull Quote | `pull_quote` | `blocks.pull-quote` | `heroicon-o-chat-bubble-bottom-center-text` |
| Embed | `embed` | `blocks.embed` | `heroicon-o-code-bracket` |
| CTA | `cta` | `blocks.cta` | `heroicon-o-cursor-arrow-rays` |

Each block defines its schema in PHP and renders via a Blade view.

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

Filament provides drag-and-drop reordering, add/remove controls, JSON serialization, and per-block form schemas out of the box.

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

## Limitations and future work

- **Media manager integration** — image blocks currently use URLs; Spatie MediaLibrary integration is deferred
- **Block-level diffs** — planned for better review mode visibility
- **Advanced block logic** — conditional rendering and nested blocks are deferred
