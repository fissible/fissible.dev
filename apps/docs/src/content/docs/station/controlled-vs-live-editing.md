---
title: Controlled vs. Live Editing
description: Per-tenant and per-content-type production editing policy, effective mode resolution, and the override permission.
sidebar:
  order: 16
---

Station supports two production-editing models. Each tenant picks a default; each content type can override. This lets a single installation support everything from fully live-edited news sites to governance-controlled enterprise content.

## The two modes

### Live

Production editing is allowed. Editors make changes directly in production. The admin UI behaves normally: create, edit, publish, and schedule all work.

Right for:

- news sites and blogs where speed matters more than governance
- small teams where the editor and the approver are the same person
- content that changes faster than a deployment cycle

### Controlled

Production editing is restricted. Editors cannot create, modify, or publish directly in production. Instead, content changes are made in a lower environment and promoted into production as a reviewed package.

Right for:

- higher-governance tenants where every change needs a paper trail
- first-party sites where git and staged promotion are the source of truth
- agency workflows where code and content deploys should be traceable but not collapsed into one mechanism
- industries with regulatory publishing requirements

In controlled mode, these actions are blocked at the service layer (not just hidden in the UI):

- Admin UI create / edit / publish actions
- Automation Engine actions that create or mutate entries
- Forms → Entry automations
- API Pro write endpoints
- any other caller of the entry service

Read-only viewing of existing entries and their version history remains available. The admin UI shows a banner on index and edit views explaining the controlled state.

## Settings

### Tenant-level

Each tenant has a `content_mode` setting:

| Value | Meaning |
|-------|---------|
| `live` | Production editing allowed by default |
| `controlled` | Production editing restricted by default |

### Content-type-level

Each content type has a `production_editing` override:

| Value | Meaning |
|-------|---------|
| `inherit` | Use the tenant-level default |
| `live` | Allow production editing for this content type |
| `controlled` | Restrict production editing for this content type |

## Effective mode resolution

For a given tenant + content type:

1. If the content type is `live` → effective mode is **live**
2. If the content type is `controlled` → effective mode is **controlled**
3. If the content type is `inherit` → effective mode comes from the tenant

This allows patterns such as:

- A mostly-controlled tenant where news posts are still live-editable
- A mostly-live tenant where legal pages are controlled
- A first-party site controlled by default

## Environment awareness

Production restrictions apply only when the current environment is treated as production.

- v1 signal: `APP_ENV === 'production'`
- Tenants on shared infrastructure may use a config-level override to mark themselves "production" independent of `APP_ENV`
- Local / dev / staging: unrestricted unless explicitly locked for testing

This means a controlled tenant can still be edited freely in dev or staging; the restrictions kick in only on the production deploy.

## Mode transitions

### live → controlled

- Existing published entries remain visible and readable.
- In-flight drafts on the affected content types become read-only; the editor sees a banner and an **export-as-package** action so the work is not stranded.
- Already-scheduled entries continue to fire on their existing schedule; controlled mode does not retroactively unschedule work.
- Users with `station.content.override_production_lock` retain full write access.

### controlled → live

No transition handling needed beyond standard permission checks.

## Override permission

Users granted `station.content.override_production_lock` (typically `admin` and `super_admin`) can bypass controlled-mode restrictions when they need to make an emergency change in production.

- Every override writes an audit record attributable to the acting user.
- v1 provides a single override mechanism — time-bounded unlocks and one-off unlock actions are deferred to a later phase.

## Imports in controlled mode

Imports are the authorized promotion path for controlled tenants. An import:

- appends a new commit to the target entry's history (never an overwrite — prior commits remain recoverable)
- enters the production workflow in a pending state if one is attached to the content type
- preserves embargo / expire timestamps as absolute UTC
- writes a full audit record attributable to the user who ran the import and the package's checksum

When a package contains both a schedule **and** a workflow, the commit enters the **scheduled-pending-approval** state:

- Schedule retained as metadata on the pending commit
- Scheduler will not fire publish until approval completes
- Approval **before** the embargo time → fires at the original scheduled time
- Approval **after** the embargo time → fires immediately on approval
- Rejection → pending commit and schedule are discarded together

## Related

- [Code vs. Content Deploy](/station/content-promotion/) — why code and content ship on separate lanes
- [Content Package CLI](/station/content-package-cli/) — export / import command reference
- [Workflows](/station/workflows/) — how production review pipelines interact with imports
- [Roles & Permissions](/station/roles-permissions/) — who gets override rights
