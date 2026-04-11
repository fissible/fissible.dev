---
title: Content Scheduling
description: Embargo and expiration dates for timed content delivery, with automatic visibility filtering.
---

Content scheduling lets editors set embargo and expiration dates on entries for timed content delivery. Embargoed entries remain hidden until a future date; expired entries are automatically hidden after a date passes.

## Opt-in per content type

Scheduling is controlled by the `scheduling_enabled` flag on each content type (default: `true`). When enabled, entries of that content type gain `embargo_at` and `expire_at` fields.

## Embargo

The `embargo_at` field sets a date and time before which the entry is hidden from the frontend. The entry must be published (or have completed its workflow), but it will not appear in frontend queries until the embargo lifts.

**Use cases:**
- Press embargoes — content ready but held until a coordinated announcement time
- Coordinated launches — multiple entries scheduled to appear simultaneously

## Expiration

The `expire_at` field sets a date and time after which the entry is automatically hidden from the frontend. The entry remains published in the admin panel but is filtered out of all public queries.

**Use cases:**
- Limited-time offers or promotions
- Event announcements that should disappear after the event
- Seasonal content with a defined shelf life

## How it works

The `Entry` model defines a `deliverable` query scope that filters out entries that are not yet ready for public display:

```php
// Pseudocode for the deliverable scope
$query->where(function ($q) {
    $q->whereNull('embargo_at')->orWhere('embargo_at', '<=', now());
})->where(function ($q) {
    $q->whereNull('expire_at')->orWhere('expire_at', '>', now());
});
```

All frontend queries — listing pages, detail pages, API endpoints, and sitemap generation — use this scope. An entry with `embargo_at` in the future is hidden; an entry with `expire_at` in the past is hidden. Entries with null values for either field are unaffected by that constraint.

## Admin UI

When scheduling is enabled for a content type, the entry edit form displays a **Scheduling** section with two `DateTimePicker` fields:

- **Embargo until** (`embargo_at`) — entry hidden until this date/time
- **Expires at** (`expire_at`) — entry hidden after this date/time

Both fields are optional. Dates respect the site timezone configured in **Site Settings**.

## Sitemap behavior

The sitemap generator (`/sitemap.xml`) uses the same `deliverable` scope. Embargoed and expired entries are excluded from the sitemap to prevent search engines from indexing content that visitors cannot access.

## Interaction with workflows

Scheduling fields are set during editing, independent of workflow state. An entry can have an `embargo_at` date set while still in review. The `deliverable` scope applies **after** the entry is published — an entry must be both published and within its scheduling window to appear on the frontend.

## Entry statuses

The scheduling system contributes to the derived entry status:

| Status | Condition |
|--------|-----------|
| Draft | Not submitted to any workflow |
| Review | Active in a workflow step (gate, review) |
| Scheduled | Published but `embargo_at` is in the future |
| Hotfix | Submitted as an urgent change |
| Published | Published and currently deliverable |

The **Scheduled** status indicates that the entry has completed its workflow (or has none) and is published, but is being held back by an embargo date. Once `embargo_at` passes, the status transitions to **Published** automatically.
