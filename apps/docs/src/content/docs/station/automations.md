---
title: Automations
description: Event-driven automations that trigger actions like creating CMS entries when forms are submitted.
sidebar:
  order: 21
---

Automations let you define rules that fire when events happen - for example, automatically creating a CMS entry when a visitor submits a form, or running an AI prompt after a CRM event. Each automation has a trigger, one or more actions, and a run log that records every execution.

## Quick overview

1. **Create an automation** in the admin panel under **Automation**
2. **Set a trigger** — what event starts the automation (e.g., form submission)
3. **Add actions** — what happens when the trigger fires (e.g., create an entry or run an AI prompt)
4. **Link to a form** — set the automation on the form's settings page
5. **Monitor runs** — each execution is logged with status and output

## Creating an automation

Navigate to **Automation** in the admin sidebar and click **New Automation**.

| Field | Description |
|-------|-------------|
| **Name** | Display name for admin reference |
| **Slug** | URL-safe identifier, unique per tenant |
| **Description** | Optional notes about what this automation does |
| **Trigger type** | The event that starts the automation |
| **Active** | Toggle to enable/disable without deleting |

### Adding actions

Actions are added via a repeater and execute in order. Each action has:

| Field | Description |
|-------|-------------|
| **Action type** | What to do (e.g., "Create Entry") |
| **Label** | Display name for this step |
| **Configuration** | Action-specific settings (see below) |

## Triggers

Triggers define what event starts the automation.

### Form Submitted

Fires when a visitor submits a form that has this automation linked. The payload contains all submitted field values.

To connect a form to an automation, edit the form and set the **Automation** field. You can also create a new automation inline from the form editor.

### CRM triggers

When the [CRM Module](/station/crm-module/) is installed, CRM events can trigger automations:

| Trigger | Fires when |
|---------|------------|
| `crm.contact.created` | A contact is created |
| `crm.company.created` | A company is created |
| `crm.deal.stage_changed` | A deal changes stages |
| `crm.deal.won` | A deal enters the terminal won stage |
| `crm.deal.lost` | A deal enters the terminal lost stage |
| `crm.activity.created` | An activity is created |
| `crm.activity.completed` | An activity is completed |

## Actions

### Create Entry

Creates a new CMS entry from the trigger payload. Configuration:

| Setting | Description |
|---------|-------------|
| **Content Type** | Which content type to create the entry in |
| **Target Status** | `draft` (default) or `published` |

#### Field mapping

The Create Entry action uses a field map to translate trigger data into entry fields. Each mapping specifies:

| Property | Description |
|----------|-------------|
| **Source type** | Where the value comes from |
| **Source key** | Which value to use |
| **Target field** | Which content type field receives the value |

**Source types:**

| Type | Description | Example source key |
|------|-------------|-------------------|
| `form_field` | Value from the form submission | The form field's internal ID |
| `static` | A fixed literal value | Any string |
| `system` | A system-generated value | `now` (ISO datetime) or `timestamp` (Unix) |

### AI: run prompt

When the [AI Module](/station/ai-module/) is installed, automations can run registered prompts with **AI: run prompt**.

Configuration:

| Setting | Description |
|---------|-------------|
| Prompt | Registered prompt key to run |
| Context | Key-value map of prompt inputs. Values can be literals or variable templates |

The action stores generated text and structured data in the action output payload, so later actions can use values such as generated text, token counts, model, agent ID, and AI run ID.

AI actions require:

- The AI Module installed
- At least one active AI agent for the tenant
- The feature enabled in **AI > AI Feature Settings**
- The current user/automation context to have permission to use AI features

## Run logging

Every automation execution is recorded as a **run** with:

- **Status** — `pending`, `running`, `completed`, or `failed`
- **Timing** — when the run started and completed
- **Error** — error message if the run failed
- **Outputs** — what each action produced (e.g., the created entry)

Each action's output records the type and ID of the resource it created, plus a human-readable summary (e.g., "Created Articles entry #42").

Failed runs log the error but do not retry automatically. Check the run log to diagnose issues.

## Permissions

| Permission | Roles |
|-----------|-------|
| View automations | Editor and above |
| Create / edit automations | Admin and above |
| Delete automations | Super Admin only |

## Extensibility

The automation engine is designed for future expansion:

- **New triggers** — modules can publish domain events such as CRM record changes
- **New actions** — custom action classes can be registered via the `ActionRegistry`
- **Conditions** — per-action conditions are supported in the data model but not yet exposed in the UI

Custom actions implement the `Action` base class with `type()`, `label()`, and `execute()` methods, then register with the `ActionRegistry` in a service provider.
