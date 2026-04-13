---
title: Workflows
description: Data-driven workflow engine with typed step handlers, configurable pipelines, and per-tenant workflow definitions.
sidebar:
  order: 22
---

Workflows define the steps content goes through before publishing — review gates, field-level diffs, notifications, scheduled holds, and the final publish. Each content type can have its own workflow, or use the tenant default, or publish directly with no pipeline.

Most sites need just one workflow (e.g., Gate → Publish, or Gate → Review → Publish). You can create multiple workflows for different content types if they need different approval processes.

## How it works

A **workflow** is a linear sequence of **steps**. Each step is typed to a **handler** that defines its behavior. When an entry is submitted, the workflow engine:

1. Snapshots the workflow's step definitions into per-entry progress records
2. Activates the first step
3. Waits for user action (or auto-advances for automated steps)
4. Advances through steps until the entry is published or rejected

Editing a workflow definition after submission does not affect in-flight entries — they use the snapshotted definitions from when they entered the pipeline.

## Workflow structure

Each workflow belongs to a tenant and consists of:

- **Name and slug** — human-readable identifier and URL-safe key
- **Steps** — ordered sequence of typed handlers with configuration
- **Default flag** — at most one workflow per tenant can be the default

Content types reference a workflow via `workflow_id`. If null, entries publish directly with no pipeline.

**Every workflow must end with a Publish step.** The engine validates this when an entry enters the pipeline.

## Step handlers

Each step in a workflow is powered by a handler. Five built-in handlers cover the common content lifecycle patterns:

### Gate

Simple approve/reject decision. The assigned reviewer (or any user with the required role) approves or rejects the entry. No field-level review — just a binary gate.

- **Actions:** approve, reject
- **Assignment:** round-robin with author-exclusion (the entry author cannot be assigned as reviewer)
- **Self-approval guard:** the entry's author cannot approve their own work (admins can bypass this via site settings)

### Review

Field-level diff review with a lock model. The reviewer sees a side-by-side diff of changes, acquires a review lock, and decides on each changed field individually.

- **Actions:** start review, approve, reject, cancel review
- **Lock:** prevents concurrent reviews (configurable TTL, default 30 minutes)
- **Field decisions:** reviewer must accept or reject each changed field (configurable)
- **UI:** renders a dedicated review diff panel

### Publish

Performs the actual publish operation — merges the draft fork into the canonical entry, transfers media, and sets the published status. This step auto-advances (no user interaction).

- **Idempotent:** each operation (merge, media transfer, status update) is checkpointed and safe to retry
- **Config:** `transfer_media` (default: true)

### Notify

Fires a notification and auto-advances. Used for mid-pipeline notifications (e.g., "notify legal after editorial approval").

- **Channels:** database, email, Slack (configurable per step)
- **Recipients:** by role, by user ID, or entry author
- **Retry:** admins can manually retry a failed notification

### Hold

Pauses the pipeline until a condition is met. Two modes:

- **Date-based:** reads a date field from the entry (e.g., `embargo_at`) and waits until that date passes
- **Manual:** waits for an explicit release action

Held entries show a status of **Scheduled** in the admin UI. A background job checks date-based holds every 5 minutes as a backup, but the primary release mechanism is eager re-evaluation on any interaction with the entry.

## Entry status

Entry status is **derived from pipeline state**, not set manually. The engine calculates status after every transition:

| Pipeline state | Entry status |
|---|---|
| No workflow assigned | Published (direct) |
| Active step, normal handler | Review |
| Active hold step with condition pending | Scheduled |
| Active step with hotfix flag | Hotfix |
| All steps completed | Published |
| Rejected, no completed steps | Draft |

## Hotfix workflow

For urgent changes, editors can submit a draft as a **hotfix**. Hotfix entries bypass the standard review queue and route to a single-step workflow requiring super admin approval. Only super admins can approve or reject a hotfix.

## Roles and assignment

Each step can specify a required role and an assigned user:

| Role requirement | Assignment | Enforcement | Who can act |
|---|---|---|---|
| None | None | — | Anyone |
| `editor` | None | — | Any editor (+ admins) |
| `editor` | User #42 | Soft (default) | Any editor; #42 is notified |
| `editor` | User #42 | Enforced | Only user #42 |

By default, assignment is informational — it highlights who's responsible but doesn't prevent others with the right role from acting. Set `enforce_assignment` in step config to restrict action to the assigned user only.

Admins and super admins bypass role and assignment checks.

## Concurrency safety

The workflow engine uses database-level locking to prevent race conditions:

- All progress rows for an entry are locked during any state transition
- An invariant enforces exactly one active step per entry at all times
- Auto-advancing steps use a depth guard to prevent infinite loops from misconfigured pipelines

## Audit trail

Two separate records track workflow history:

- **Workflow transitions** — records every entry status change (draft to review, review to published, etc.). This is the high-level timeline.
- **Entry workflow progress** — records per-step state including who decided, when, and handler-specific data (field decisions, rejection reasons, etc.). This is the detailed record.

A step completion that doesn't change entry status (e.g., a gate approval followed by another gate) produces no transition record. This is intentional — transitions track status changes, not step events.

## Content type configuration

Content types reference workflows through a `workflow_id` foreign key:

- `workflow_id = null` — entries publish directly, no pipeline
- `workflow_id` set — entries go through the referenced workflow on submission

If the tenant has a default workflow and the content type has no explicit assignment, the default workflow is used.

## Admin UI

Workflows appear as **Pipelines** in the admin sidebar under the **Automation** group. Management is available in development environments only:

- **List view:** pipelines with step count, linked content types, and default badge
- **Create/Edit:** name, slug, description, default toggle, and an orderable step repeater
- **Step repeater:** each step configures handler type, label, required role, and handler-specific settings
- **Validation:** the last step must be a Publish handler

Handler-specific configuration forms are rendered dynamically based on the selected handler type. For example, the Review handler shows lock TTL and field decision requirements.

## Limitations and future work

- **Linear pipelines only** — steps execute in sequence (1, 2, 3...). Branching and DAG workflows are a future upgrade. The handler contract is designed to support this without breaking changes.
- **Per-tenant only** — each tenant defines its own workflows. No global workflow templates or cross-tenant sharing.
- **No automated retry** — if a handler (Notify, Publish) fails, the step stays active and an admin must manually retry. Automated retry is a future enhancement.
