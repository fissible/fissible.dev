---
title: Seeder Suites
description: Industry-specific content presets for quick demos and starting points, with seed and delete operations managed through the platform panel.
---

Seeder suites are industry-specific content presets that populate a tenant with realistic content types, entries, and page hierarchies. They are designed for quick demos and as starting points for new sites.

## Available suites

| Suite | Slug | Industry |
|-------|------|----------|
| **Valley Stage Performing Arts** | `valley-stage` | Theater / arts center |
| **Desert Sky Brewing** | `desert-sky` | Brewery / hospitality |
| **Mitchell Family Insurance** | `mitchell-family` | Professional services |

Each suite creates content types with field definitions, entries with realistic sample data, and page hierarchies with parent-child relationships.

## Suite structure

Every suite implements the `SeederSuite` interface:

```php
interface SeederSuite
{
    public function name(): string;
    public function slug(): string;
    public function description(): string;
    public function contentTypes(): array;
    public function entries(): array;
}
```

### What gets created

- **Content types** — with field definitions (text, textarea, image, etc.)
- **Entries** — with realistic sample data populated in the `data` column
- **Page hierarchies** — parent-child relationships between page entries

All created records are tagged with a `seed_group` value matching the suite slug, allowing the runner to track and remove them cleanly.

## Runner mechanism

The `SeederSuiteRunner` service handles both seeding and deletion.

### Seed operation

1. Creates content types from the suite definition
2. Creates entries for each content type
3. Resolves parent-child hierarchies for pages
4. All operations run within a database transaction

### Delete operation

1. Removes entries (including any draft forks)
2. Removes content types created by the suite
3. All operations run within a database transaction

## Admin UI

The seeder suites management page is located at **Platform panel > System > Seeder Suites**.

| Element | Description |
|---------|-------------|
| Suite table | Displays name, slug, description for each discovered suite |
| Status badge | **Seeded** or **Not seeded** based on whether the suite's records exist |
| Seed button | Creates all content for the suite |
| Delete button | Removes all content created by the suite |

**Access requirements:**

- User must have the `super_admin` role
- Application must be running in a `local` or `dev` environment

## Creating custom suites

To add a custom suite:

1. Create a class that implements the `SeederSuite` interface
2. Place it in `database/seeders/Suites/`
3. The runner auto-discovers all classes in that directory

```php
namespace Database\Seeders\Suites;

use App\Contracts\SeederSuite;

class MyCustomSuite implements SeederSuite
{
    public function name(): string
    {
        return 'My Custom Suite';
    }

    public function slug(): string
    {
        return 'my-custom';
    }

    public function description(): string
    {
        return 'A custom demo suite.';
    }

    public function contentTypes(): array
    {
        return [
            // Content type definitions...
        ];
    }

    public function entries(): array
    {
        return [
            // Entry definitions...
        ];
    }
}
```
