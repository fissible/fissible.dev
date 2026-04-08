---
title: Station
description: Self-hosted Laravel CMS and workflow platform with multi-tenancy, modular architecture, and admin UI powered by Filament.
---

Station is a self-hosted CMS and workflow platform built on Laravel 12. It provides a modular architecture where CMS, Flow, and future modules dock in via a shared platform core — getting sidebar navigation, global search, and authentication for free.

## Architecture

Station is organized into layers:

- **Laravel Foundation** — Laravel 12, PHP 8.4, MySQL, Redis, Queues, Sanctum, Spatie Media Library, Spatie Permissions
- **Platform Core** — Module Registry, Nav Registry, Search Registry, Auth/Roles, and the `PlatformModule` contract. A Filament Adapter translates platform contracts into Filament v5 resources.
- **Modules** — Each module implements `PlatformModule` and registers its own resources, navigation, and search providers.
- **Admin UI** — Filament v5 + Livewire for all admin panels.
- **Content Delivery** — Blade templates + REST API for the public-facing site.

## Modules

| Module | Status | Description |
|--------|--------|-------------|
| CMS | v0.4 | Content types, entries, versioning, media, menus, scheduling, SEO/sitemaps |
| Flow | v0.4 | Workflow engine for content review, hotfix approval, and automation |
| Pilot | Planned | Developer tools and deployment management |

## Multi-tenancy

Station supports multiple tenants on a single installation. Each tenant gets:

- Isolated content, media, and configuration
- Independent admin panel with role-based access
- Per-tenant backup and restore capabilities

Platform administrators manage all tenants from a separate platform admin panel.

## Key concepts

- **Content Types** — define the shape of your content (fields, validation, relationships)
- **Entries** — instances of a content type, with full versioning history and content scheduling
- **Menus** — drag-and-drop menu builder with nested items, dynamic expansion, and role-based visibility
- **Flows** — workflow definitions that govern content lifecycle (review, hotfix approval, publishing)
- **Modules** — self-contained feature packages that plug into the platform core
- **Maintenance Mode** — per-tenant toggle that shows a custom page to unauthenticated visitors
- **Account Self-Service** — password changes, avatar upload, TOTP two-factor authentication, and account deletion
