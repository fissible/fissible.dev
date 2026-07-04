---
title: CRM Module
description: Contacts, companies, deals, activities, timelines, and CRM workflow triggers for Station tenants.
sidebar:
  order: 57
---

The CRM Module adds tenant-scoped sales and relationship management to Station. It is built as an installable Station module and integrates with Station permissions, global search, and automations.

CRM is a premium module. Tenants without an active CRM entitlement can still be allowed read-only access depending on license state, but mutation actions require an active license.

## What v1 includes

| Area | Capability |
|------|------------|
| Contacts | People records with email, phone, title, company association, owner, and notes |
| Companies | Account records with domain normalization, phone, owner, custom fields, and soft delete |
| Deals | Pipeline deals with amount, expected close date, stage, probability, owner, primary contact, and primary company |
| Stages | Default pipeline stages with ordering, probability, and terminal won/lost markers |
| Activities | Notes, calls, emails, meetings, and tasks associated to contacts, companies, and deals |
| Timeline | Read-only timeline relation on contact, company, and deal edit pages |
| Workflows | Curated CRM workflow page plus CRM automation triggers |
| Search | Contacts and companies participate in Station global search |

## Install and enable

Install the package and run the module lifecycle:

```bash
composer require fissible/crm
php artisan station:module:install crm
```

Platform admins can also install the module from **Platform > Modules** when the package is available to the Station installation.

After installation, users with CRM permissions see a **CRM** navigation group in the tenant admin panel.

## Contacts

Contacts represent people.

Main fields:

| Field | Description |
|-------|-------------|
| First name / Last name | Name components used for display and sorting |
| Email | Searchable and copyable email address |
| Phone | Telephone number |
| Company | Optional company association; can create a company inline |
| Title | Job title or relationship label |
| Notes | Simple notes stored in custom fields |

Contacts respect tenant visibility and owner-aware permissions. Users with `crm.contacts.view-all` can see all tenant contacts; other users see records allowed by the CRM visibility policy.

## Companies

Companies represent organizations or accounts.

Main fields:

| Field | Description |
|-------|-------------|
| Name | Required company name |
| Domain | Normalized domain without protocol |
| Phone | Company phone number |
| Owner | Assigned tenant user |
| Custom fields | Key-value data for lightweight tenant-specific fields |

Companies are soft-deleted. Archived companies can still appear in related records with an archived indicator.

## Deals and stages

Deals track opportunities through a pipeline.

Main fields:

| Field | Description |
|-------|-------------|
| Name | Required deal name |
| Amount | Deal value in the configured CRM currency |
| Expected close date | Date forecast for close |
| Pipeline | Pipeline the deal belongs to |
| Stage | Current stage within the pipeline |
| Probability | Optional override; blank inherits the stage default |
| Owner | Assigned tenant user |
| Primary contact/company | Main relationship for the deal |
| Custom fields | Key-value data for lightweight tenant-specific fields |

The module creates or ensures a default pipeline for each tenant. Stages can be reordered, assigned default probabilities, and marked as terminal won or terminal lost from the stage edit page.

Configure the displayed currency with:

```ini
CRM_DEFAULT_CURRENCY=USD
```

## Activities and timeline

Activities record relationship history and follow-up work.

Activity types:

- Note
- Call
- Email
- Meeting
- Task

Activity fields include subject, body, occurred time, status, optional due date for tasks, owner, and associations to contacts, companies, and deals.

The activity timeline appears on contact, company, and deal edit pages. The timeline is read-only; create and edit activities from the activities relation manager or the main Activities resource.

## CRM workflows

The **CRM > Workflows** page shows automations whose trigger type starts with `crm.`. It is a focused view over the Station automation engine, not a separate workflow system.

Supported CRM triggers include:

| Trigger | Fires when |
|---------|------------|
| `crm.contact.created` | A contact is created |
| `crm.company.created` | A company is created |
| `crm.deal.stage_changed` | A deal changes stages |
| `crm.deal.won` | A deal enters the terminal won stage |
| `crm.deal.lost` | A deal enters the terminal lost stage |
| `crm.activity.created` | An activity is created |
| `crm.activity.completed` | An activity is completed |

Use these triggers with regular Station automation actions, including CMS entry creation and AI prompt runs when the AI Module is installed.

## Permissions

CRM permissions are namespaced by resource.

| Area | Permissions |
|------|-------------|
| Contacts | `crm.contacts.view`, `view-all`, `create`, `edit`, `delete` |
| Companies | `crm.companies.view`, `view-all`, `create`, `edit`, `delete` |
| Deals | `crm.deals.view`, `view-all`, `create`, `edit`, `delete` |
| Stages | `crm.stages.manage` |
| Activities | `crm.activities.view`, `view-all`, `create`, `edit`, `delete` |

Default role mapping:

- Authors can view and create CRM records.
- Editors can view, create, and edit CRM records.
- Admins and Super Admins can view all, create, edit, delete, and manage stages.

## Operational notes

- CRM data is tenant-scoped.
- Contacts, companies, deals, and activities support soft-delete behavior where applicable.
- CRM integrates with Station global search for contacts and companies.
- CRM workflows use Station's automation logs for troubleshooting.
- If mutation buttons disappear, check the tenant license/module state and the user's CRM permissions.
