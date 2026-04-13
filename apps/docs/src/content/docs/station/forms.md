---
title: Forms
description: Build forms, collect submissions, block spam, and trigger automations when visitors submit data.
sidebar:
  order: 20
---

Station includes a form builder for collecting visitor submissions. Forms are managed in the admin panel, rendered on the frontend with spam defenses built in, and can trigger automations when submitted.

## Quick overview

1. **Create a form** in the admin panel under **Forms > Forms**
2. **Add fields** — text, email, textarea, select, radio, checkbox, date, and more
3. **Set status to Active** — makes the form available at `/forms/{slug}`
4. **Embed in content** (optional) — add a Form content block to any entry
5. **View submissions** in the admin panel under **Forms > Submissions**

## Creating a form

Navigate to **Forms > Forms** and click **New Form**. The form editor has two sections:

![Form builder showing field repeater with name and message fields, submit button label, and automation selector](/station/form-builder.png)

### Settings

| Field | Description |
|-------|-------------|
| **Name** | Display name for admin reference |
| **Slug** | URL-safe identifier — becomes the form URL (`/forms/{slug}`). Locked once the form is Active. |
| **Status** | `Draft` (not publicly accessible) or `Active` (accepting submissions) |
| **Success message** | Shown to visitors after a successful submission (default: "Thank you for your submission.") |
| **Submit button label** | Button text on the public form (default: "Submit") |
| **Automation** | Optional — trigger an automation when the form is submitted (see [Automations](/station/automations/)) |

### Fields

Add fields using the repeater. Each field has:

| Property | Description |
|----------|-------------|
| **Name** | Internal identifier (lowercase, no spaces) |
| **Label** | Display label shown to visitors |
| **Type** | Input type (see field types below) |
| **Required** | Whether the field must be filled |
| **Placeholder** | Hint text inside the input |
| **Options** | For select, radio, and checkbox types — one option per line |
| **Default value** | For hidden fields — a preset value included in every submission |

### Field types

| Type | Input | Stored as |
|------|-------|-----------|
| `text` | Single-line text input | String (max 500) |
| `email` | Email input with validation | String |
| `textarea` | Multi-line text area | String (max 5,000) |
| `phone` | Phone number input | String (max 30) |
| `select` | Dropdown menu | Selected option value |
| `radio` | Radio button group | Selected option value |
| `checkbox` | Checkbox group | Array of selected values |
| `date` | Date picker | `YYYY-MM-DD` |
| `datetime` | Date and time picker | ISO datetime |
| `hidden` | Not displayed, carries a default value | String |

## Form URLs

Active forms are accessible at:

```
GET  /forms/{slug}     → displays the form
POST /forms/{slug}     → processes submission
```

Draft forms return a 404 to visitors.

## Embedding in content

Forms can be embedded in any entry that has a `content_blocks` field. Add a **Form** block and select an active form from the dropdown. The form renders inline within the entry's content.

The Form block inherits the standard block options (theme and width) so it matches the surrounding content.

## Viewing submissions

Navigate to **Forms > Submissions** to see all submissions across forms, newest first.

Each submission shows:
- The first two field values as a preview
- Read/unread status (submissions are marked as read when you view them)
- Submitter IP address
- Timestamp

Click a submission to see the full response with all field values and metadata.

### Filtering

- **By form** — filter to a specific form's submissions
- **By read status** — show only unread submissions

The Forms navigation badge shows the count of unread submissions.

## Spam defense

Station uses three layers of spam protection:

### Honeypot field

Every form includes a hidden honeypot field. Bots that fill it in are silently rejected — the response looks identical to a successful submission. No configuration needed.

### IP blocking

Block specific IP addresses from submitting. When a blocked IP submits, the form processes normally (same timing, same validation) but silently discards the submission.

Block an IP from the submissions list by clicking the **Block IP** action on any submission. Blocks are per-form and permanent until manually removed.

### Rate limiting

Submissions are rate-limited to **5 per minute per IP per form**. Exceeding the limit returns a 429 response.

## Automation integration

Link a form to an automation to trigger actions when visitors submit. For example, automatically create a CMS entry from form data.

Set the **Automation** field on the form to an existing automation, or create one inline from the form editor. See [Automations](/station/automations/) for details on setting up triggers and actions.

## Permissions

| Permission | Roles |
|-----------|-------|
| View forms | Reviewer and above |
| Create / edit forms | Editor and above |
| Delete forms | Admin and above |
| View submissions | Author and above |
| Delete submissions | Editor and above |
| Manage IP blocks | Admin and above |

## Field ID stability

Internally, each field is assigned a stable ID (e.g., `fld_a1b2c3d4e5f6`) when the form is created. Submission data is stored keyed by this ID, not the field name. This means you can safely rename a field's name or label without breaking existing submission data.

Each submission also stores a snapshot of the form's field definitions at the time of submission, ensuring historical submissions always display correctly even if the form is later modified.
