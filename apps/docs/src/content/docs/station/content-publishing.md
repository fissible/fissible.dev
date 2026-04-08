---
title: Content Publishing
description: Entry lifecycle from draft through review to publishing, including versioning, draft forks, commit history, scheduling, and hotfix workflow.
---

Station uses a **draft fork** model for content publishing. When a published entry is edited, the system creates a separate draft that goes through review before replacing the live version. Every publish creates an append-only commit snapshot, enabling rollback to any prior state.

## Entry lifecycle

```
Create → Draft → Submit → Review → Publish
                    ↑                  │
                    └── Reject ────────┘
```

New entries start as **drafts**. Submitting a draft enters it into the assigned workflow pipeline (see [Workflows](/station/workflows/)). If no workflow is assigned to the content type, entries publish directly.

## Draft forks

When a published entry is edited, Station creates a **draft fork** rather than modifying the live entry. The fork:

- References the canonical (live) entry via `version_of_id`
- Inherits the current slug (which can diverge during editing)
- Goes through the full workflow pipeline independently
- Is visible only in the admin panel, never on the public site

A **unique constraint** ensures only one draft fork exists per entry at a time. Fork creation is race-safe — concurrent edit attempts are caught by the database constraint.

### Fork lifecycle

1. **Editor clicks "Edit" on a published entry** — a draft fork is created with the current content
2. **Editor makes changes** — changes are saved to the fork, not the live entry
3. **Editor submits for review** — the fork enters the workflow pipeline
4. **Reviewer approves** — the fork is merged into the canonical entry (see [Publishing](#publishing) below)
5. **Reviewer rejects** — the fork returns to draft status for further editing

### Discarding a fork

Editors can discard a draft fork, which deletes it and leaves the canonical entry unchanged. If a published entry is unpublished while a fork exists, a confirmation modal warns that the fork will be discarded.

## Publishing

When a draft fork is approved through the workflow, the **Publish step handler** performs these operations in a single database transaction:

1. **Merge fork into canonical** — the fork's content overwrites the canonical entry's data
2. **Transfer media** — any media attached to the fork is re-pointed to the canonical entry
3. **Create commit** — an append-only snapshot is saved (see [Commit history](#commit-history))
4. **Delete fork** — the fork record is removed
5. **Set published status** — the canonical entry is marked as published with timestamps

Each operation is checkpointed and idempotent — if the process is interrupted, it can safely resume from where it left off.

### Slug handling

The fork inherits the canonical entry's slug on creation. Editors can change the slug during editing. On publish, the fork's slug overwrites the canonical's slug. If a slug collision would occur, the system validates before the transaction and returns a validation error to the editor.

## Commit history

Every publish creates an **entry commit** — an append-only snapshot of the entry's state:

- `data` — the full entry content (JSON)
- `slug` — the entry's slug at that point
- `parent_id` — the entry's hierarchy position

Commits are tenant-scoped and never modified after creation. They provide a complete audit trail of every published version.

### Rollback

Admins and super admins can rollback an entry to any prior commit:

1. Select a commit from the history panel on the entry edit page
2. The system restores the entry's data, slug, and hierarchy from the commit
3. If a draft fork exists, it is discarded (with confirmation)
4. If a slug collision occurs during rollback, everything except the slug is restored and the editor is notified

Rollback is a privileged operation — only `admin` and `super_admin` roles can perform it.

## Content scheduling

Entries support two scheduling fields, opt-in per content type (`scheduling_enabled`):

### Embargo (`embargo_at`)

The entry is published but hidden from content delivery until the embargo date passes. Use this for timed releases — the entry is approved and ready, but not yet visible to the public.

### Expiration (`expire_at`)

The entry is automatically hidden from content delivery after the expiration date. Use this for time-limited content like event announcements or promotions.

### How scheduling works

- Dates respect the site timezone configured in Site Settings
- The `deliverable` scope on the Entry model filters out embargoed and expired entries automatically
- In the workflow pipeline, scheduling is handled by the **Hold step handler** — it pauses the pipeline until the embargo date passes, then auto-advances to the Publish step
- A background job checks date-based holds every 5 minutes as a backup

The entry status shows as **Scheduled** while waiting for an embargo date.

## Hotfix workflow

For urgent changes that can't wait for the standard review process:

1. An editor submits a draft fork as a **hotfix**
2. The hotfix bypasses the standard review queue
3. It routes directly to a single-step approval gate requiring **super admin** approval
4. Only super admins can approve (publish) or reject (return to draft) a hotfix

Hotfix entries show a distinct **Hotfix** status badge in the admin panel.

## Entry statuses

| Status | Meaning |
|--------|---------|
| Draft | Not yet submitted, or rejected and returned for editing |
| Review | In the workflow pipeline, awaiting reviewer action |
| Scheduled | Approved but held for a future date (embargo) |
| Hotfix | Submitted as urgent, awaiting super admin approval |
| Published | Live and visible on the public site (subject to scheduling) |

Statuses are **derived from workflow pipeline state** — they are never set manually. See [Workflows](/station/workflows/) for details on how status derivation works.

## Preview

Authenticated users can preview entries (including draft forks) via an authenticated preview route. The preview loads the fork if one exists, otherwise the canonical entry. Preview URLs are not accessible to unauthenticated visitors.

## Soft deletes

Entries use soft deletes. When a canonical entry is deleted:

- Any active draft fork is also soft-deleted automatically
- The entry is removed from public content delivery
- Trash management UI is planned for a future release
