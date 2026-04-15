# Station Web App Scaffold — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Convert fissible.dev from static Astro sites to a Laravel application, port marketing content to Blade views, and prepare for Forge deployment.

**Architecture:** Laravel 12 at repo root with Blade views for public pages. Marketing content ported from Astro components. SQLite for local dev, MySQL for production via Forge. The existing `apps/site/` stays as reference during porting, then gets removed. `apps/docs/` stays as a separate Starlight deploy for now.

**Tech Stack:** Laravel 12, Blade, Vite, vanilla CSS (ported from Astro), Formspree (forms), Laravel Forge + DigitalOcean VPS

---

## File Structure

### New files (Laravel scaffold + marketing views)

```
/                           # Repo root becomes Laravel root
  app/
    Http/
      Controllers/
        MarketingController.php    # Homepage, Station, tools, coming-soon
    Providers/
      AppServiceProvider.php       # Laravel default
  bootstrap/
    app.php
  config/                          # Laravel config files
  database/
    migrations/
    seeders/
  public/
    index.php                      # Laravel entry point
    station/                       # Station diagram SVGs (migrated)
    screenshots/                   # Tool screenshots (migrated)
    shellql/                       # ShellQL assets (migrated)
    favicon.ico
    favicon.svg
  resources/
    css/
      app.css                      # Ported from apps/site/src/styles/global.css
    js/
      app.js                       # Vite entry + workflow animation
    views/
      layouts/
        marketing.blade.php        # Base layout (Nav, footer, meta)
      components/
        nav.blade.php              # Sticky nav
        announcement-bar.blade.php # Top banner
        hero.blade.php             # Homepage hero
        workflow-animation.blade.php
        use-case.blade.php
        tech-credibility.blade.php
        ecosystem.blade.php
        converge-cta.blade.php
        marketing-page.blade.php   # Generic tool page template
        coming-soon-page.blade.php
      pages/
        home.blade.php
        station.blade.php
        tools/
          index.blade.php
          show.blade.php           # Individual tool page
        coming-soon.blade.php      # guit, sigil, conduit
  routes/
    web.php                        # All public routes + redirects
  .env.example
  artisan
  composer.json
  vite.config.js
```

### Files to keep during transition
```
  apps/site/                       # Reference for content porting (remove in final task)
  apps/docs/                       # Stays permanently for now (separate deploy)
```

### Files to remove/replace
```
  package.json                     # Current Node workspace config → replaced by Laravel's
  package-lock.json                # Regenerated (root only; apps/docs keeps its own)
  vercel.json                      # Redirects move to routes/web.php
  node_modules/                    # Regenerated (Vite deps only)
```

### Docs continuity requirement

`apps/docs/` must remain independently buildable after the root workspace is removed.
Before deleting the root `package.json` workspace config:
1. Ensure `apps/docs/package.json` has all its own dependencies (not inherited from root)
2. Generate `apps/docs/package-lock.json` via `cd apps/docs && npm install --package-lock-only`
3. Verify `cd apps/docs && npm ci && npm run build` succeeds standalone

This is handled in Task 1, Step 3.

---

## Task 1: Scaffold Laravel at repo root

**Files:**
- Create: `composer.json`, `artisan`, `app/`, `bootstrap/`, `config/`, `database/`, `public/index.php`, `resources/`, `routes/`, `storage/`, `.env.example`, `vite.config.js`
- Create: `apps/docs/package-lock.json` (standalone lockfile for docs)
- Remove: root `package.json`, root `package-lock.json` (via git rm, not shell rm)
- Modify: `.gitignore`

- [ ] **Step 1: Scaffold Laravel in a temp directory**

```bash
cd /tmp
composer create-project laravel/laravel fissible-scaffold --prefer-dist
```

- [ ] **Step 2: Copy Laravel skeleton into repo root**

Copy all Laravel files into the repo root, excluding `.git`, `.gitignore` (we'll merge), and `README.md`:

```bash
cd /tmp/fissible-scaffold
# Copy directories
for dir in app bootstrap config database public resources routes storage tests; do
  cp -r "$dir" /Users/allenmccabe/lib/fissible/fissible.dev/
done
# Copy root files
for f in artisan composer.json composer.lock phpunit.xml .env.example vite.config.js package.json; do
  cp "$f" /Users/allenmccabe/lib/fissible/fissible.dev/
done
```

- [ ] **Step 3: Make docs standalone before removing root workspace**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev/apps/docs
npm install --package-lock-only
npm ci && npm run build
```

Verify the build succeeds. Then remove root workspace files via git (not shell rm):

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
git rm package.json package-lock.json
rm -rf node_modules  # untracked, safe to shell-rm
```

- [ ] **Step 4: Update .gitignore for Laravel**

Replace `.gitignore` with Laravel defaults plus existing entries:

```gitignore
/vendor/
/node_modules/
/public/hot
/public/storage
/public/build
/storage/*.key
/storage/debugbar
/storage/pail
/.env
/.env.backup
/.env.production
/.phpactor.json
/.phpunit.cache
/auth.json
/npm-debug.log
/yarn-error.log
/.fleet
/.idea
/.vscode

# Keep existing
*.DS_Store
.superpowers/
.tome.db
.tome.db-shm
.tome.db-wal
.worktrees/
dist/
.astro/
```

- [ ] **Step 5: Install PHP dependencies**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
composer install
```

- [ ] **Step 6: Set up local environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` to use SQLite:
```
DB_CONNECTION=sqlite
# DB_DATABASE is auto-detected as database/database.sqlite
```

```bash
touch database/database.sqlite
php artisan migrate
```

- [ ] **Step 7: Install Node dependencies and verify Vite**

```bash
npm install
```

- [ ] **Step 8: Smoke test**

```bash
php artisan serve &
curl -s http://localhost:8000 | head -20
# Should show Laravel welcome page HTML
kill %1
```

- [ ] **Step 9: Commit**

```bash
git add -A
git commit -m "feat: scaffold Laravel 12 at repo root

Replaces Node workspace with Laravel application.
Astro apps preserved in apps/ for reference during migration.

Refs: fissible/fissible.dev#5"
```

---

## Task 2: Port base layout and CSS

**Files:**
- Create: `resources/css/app.css`, `resources/views/layouts/marketing.blade.php`, `resources/views/components/nav.blade.php`, `resources/views/components/announcement-bar.blade.php`
- Modify: `vite.config.js`
- Reference: `apps/site/src/styles/global.css`, `apps/site/src/layouts/Layout.astro`, `apps/site/src/components/Nav.astro`, `apps/site/src/components/AnnouncementBar.astro`

- [ ] **Step 1: Port global CSS**

Copy the CSS from `apps/site/src/styles/global.css` into `resources/css/app.css`. This is the dark-theme design system with CSS variables:

```css
/* resources/css/app.css */
/* Port contents of apps/site/src/styles/global.css verbatim */
/* Then append component-specific styles from each Astro component's <style> blocks */
```

Read the source: `apps/site/src/styles/global.css` (137 lines) and all `<style>` blocks from Astro components to consolidate into one CSS file.

- [ ] **Step 2: Create marketing layout**

Create `resources/views/layouts/marketing.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'fissible' }} — fissible</title>
    <meta name="description" content="{{ $description ?? 'Self-hosted CMS for teams that need approval before anything goes live.' }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-announcement-bar />
    <x-nav />
    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

- [ ] **Step 3: Create nav component**

Create `resources/views/components/nav.blade.php`. Port from `apps/site/src/components/Nav.astro`:

```blade
<nav class="nav">
    <div class="nav-inner">
        <a href="/" class="nav-logo">
            <span class="nav-logo-text">fissible</span>
        </a>
        <div class="nav-links">
            <a href="/station" class="{{ request()->is('station') ? 'active' : '' }}">Station</a>
            <a href="/tools" class="{{ request()->is('tools*') ? 'active' : '' }}">Tools</a>
            <a href="https://docs.fissible.dev" target="_blank" rel="noopener">Docs</a>
            <a href="https://github.com/fissible" target="_blank" rel="noopener">GitHub</a>
        </div>
    </div>
</nav>
```

- [ ] **Step 4: Create announcement bar component**

Create `resources/views/components/announcement-bar.blade.php`. Port from `apps/site/src/components/AnnouncementBar.astro`.

- [ ] **Step 5: Verify layout renders**

Create a temporary route in `routes/web.php`:

```php
Route::view('/', 'pages.home');
```

Create `resources/views/pages/home.blade.php`:

```blade
<x-layouts.marketing title="Home">
    <h1>Layout works</h1>
</x-layouts.marketing>
```

```bash
php artisan serve &
curl -s http://localhost:8000 | grep "Layout works"
kill %1
```

- [ ] **Step 6: Commit**

```bash
git add resources/css/app.css resources/views/layouts/ resources/views/components/nav.blade.php resources/views/components/announcement-bar.blade.php resources/views/pages/home.blade.php routes/web.php vite.config.js
git commit -m "feat: port base layout, nav, and CSS from Astro site

Dark theme, sticky nav, announcement bar ported as Blade components.
All CSS consolidated into resources/css/app.css."
```

---

## Task 3: Port homepage

**Files:**
- Create: `resources/views/components/hero.blade.php`, `resources/views/components/workflow-animation.blade.php`, `resources/views/components/use-case.blade.php`, `resources/views/components/tech-credibility.blade.php`, `resources/views/components/ecosystem.blade.php`, `resources/views/components/converge-cta.blade.php`
- Modify: `resources/views/pages/home.blade.php`, `resources/js/app.js`
- Reference: `apps/site/src/pages/index.astro` and all component files it imports

- [ ] **Step 1: Read the Astro homepage and all its component imports**

Read these files to understand the exact content and structure:
- `apps/site/src/pages/index.astro`
- `apps/site/src/components/Hero.astro`
- `apps/site/src/components/WorkflowAnimation.astro`
- `apps/site/src/components/UseCase.astro`
- `apps/site/src/components/TechCredibility.astro`
- `apps/site/src/components/Ecosystem.astro`
- `apps/site/src/components/ConvergeCTA.astro`

- [ ] **Step 2: Create hero component**

Port `Hero.astro` to `resources/views/components/hero.blade.php`. Include the Formspree waitlist form, headline copy, and proof points. The form action posts to Formspree.

- [ ] **Step 3: Create workflow animation component**

Port `WorkflowAnimation.astro` to `resources/views/components/workflow-animation.blade.php`. The Intersection Observer JS that triggers the step-by-step animation goes into `resources/js/app.js`.

- [ ] **Step 4: Create use-case, tech-credibility, and ecosystem components**

Port each to its Blade equivalent:
- `resources/views/components/use-case.blade.php`
- `resources/views/components/tech-credibility.blade.php`
- `resources/views/components/ecosystem.blade.php`

- [ ] **Step 5: Create converge CTA component**

Port `ConvergeCTA.astro` to `resources/views/components/converge-cta.blade.php`. Include the waitlist form.

- [ ] **Step 6: Assemble the homepage**

Update `resources/views/pages/home.blade.php`:

```blade
<x-layouts.marketing title="fissible station" description="The self-hosted CMS for teams that need approval before anything goes live.">
    <x-hero />
    <x-workflow-animation />
    <x-use-case />
    <x-tech-credibility />
    <x-ecosystem />
    <x-converge-cta />
</x-layouts.marketing>
```

- [ ] **Step 7: Verify in browser**

```bash
npm run build && php artisan serve
```

Open `http://localhost:8000` in a browser. Verify:
- Dark theme renders
- Nav is sticky
- Hero section shows with waitlist form
- Workflow animation triggers on scroll
- All sections display correctly
- Responsive at mobile widths

- [ ] **Step 8: Commit**

```bash
git add resources/views/ resources/js/app.js
git commit -m "feat: port homepage from Astro to Blade

All six homepage sections ported: hero, workflow animation,
use cases, tech credibility, ecosystem, and CTA."
```

---

## Task 4: Create MarketingController and port tool data

**Files:**
- Create: `app/Http/Controllers/MarketingController.php`, `config/packages.php`
- Modify: `routes/web.php`
- Reference: `apps/site/src/data/packages.ts`

- [ ] **Step 1: Create packages config**

Port the TypeScript data from `apps/site/src/data/packages.ts` to a PHP config file at `config/packages.php`:

```php
<?php
// config/packages.php

return [
    'tui' => [
        [
            'slug' => 'shellframe',
            'name' => 'shellframe',
            'tagline' => 'TUI framework for bash',
            'install' => 'brew install fissible/tap/shellframe',
            'install_label' => 'brew',
            // ... all fields from tuiPackages
        ],
        // ... seed, ptyunit, shellql
    ],
    'php' => [
        // ... accord, drift, forge
    ],
    'paid' => [
        // ... guit, sigil, station, conduit
    ],
];
```

Port ALL content from the TypeScript arrays. Every field, every marketing section, every feature.

- [ ] **Step 2: Create MarketingController**

```php
<?php
// app/Http/Controllers/MarketingController.php

namespace App\Http\Controllers;

class MarketingController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function station()
    {
        $station = collect(config('packages.paid'))
            ->firstWhere('slug', 'station');

        return view('pages.station', compact('station'));
    }

    public function toolsIndex()
    {
        return view('pages.tools.index', [
            'tuiPackages' => config('packages.tui'),
            'phpPackages' => config('packages.php'),
        ]);
    }

    public function toolShow(string $slug)
    {
        $package = collect(config('packages.tui'))
            ->merge(config('packages.php'))
            ->firstWhere('slug', $slug);

        abort_unless($package, 404);

        return view('pages.tools.show', compact('package'));
    }

    public function comingSoon(string $slug)
    {
        $product = collect(config('packages.paid'))
            ->firstWhere('slug', $slug);

        abort_unless($product, 404);

        return view('pages.coming-soon', compact('product'));
    }
}
```

- [ ] **Step 3: Define routes**

Update `routes/web.php`:

```php
<?php

use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

// Marketing pages
Route::get('/', [MarketingController::class, 'home']);
Route::get('/station', [MarketingController::class, 'station']);
Route::get('/tools', [MarketingController::class, 'toolsIndex']);
Route::get('/tools/{slug}', [MarketingController::class, 'toolShow']);

// Coming soon products
Route::get('/guit', [MarketingController::class, 'comingSoon'])
    ->defaults('slug', 'guit');
Route::get('/sigil', [MarketingController::class, 'comingSoon'])
    ->defaults('slug', 'sigil');
Route::get('/conduit', [MarketingController::class, 'comingSoon'])
    ->defaults('slug', 'conduit');

// Redirects (from vercel.json) — only for tools that have pages
Route::permanentRedirect('/accord', '/tools/accord');
Route::permanentRedirect('/drift', '/tools/drift');
Route::permanentRedirect('/forge', '/tools/forge');
Route::permanentRedirect('/seed', '/tools/seed');
Route::permanentRedirect('/shellframe', '/tools/shellframe');
Route::permanentRedirect('/ptyunit', '/tools/ptyunit');
Route::permanentRedirect('/shellql', '/tools/shellql');

// /watch and /fault were deprecated modules removed from docs (commit 05d4d82).
// Redirect to tools index rather than to nonexistent /tools/watch and /tools/fault pages.
Route::permanentRedirect('/watch', '/tools');
Route::permanentRedirect('/fault', '/tools');
```

- [ ] **Step 4: Verify routes resolve**

```bash
php artisan route:list --columns=method,uri,name
```

Expected: all routes listed with correct URIs.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/MarketingController.php config/packages.php routes/web.php
git commit -m "feat: add MarketingController, package data config, and routes

Tool/product data ported from TypeScript to PHP config.
All public routes and Vercel redirects defined."
```

---

## Task 5: Port Station product page

**Files:**
- Create: `resources/views/pages/station.blade.php`
- Reference: `apps/site/src/pages/station/index.astro`

- [ ] **Step 1: Read the Astro Station page**

Read `apps/site/src/pages/station/index.astro` to understand the full structure: kicker, headline, decision cards, comparison points, workflow diagram, objection responses, feature blocks, use-case callout, marketing sections, and waitlist form.

- [ ] **Step 2: Create the Station Blade view**

Create `resources/views/pages/station.blade.php` with all sections ported. Content comes from the `$station` variable passed by the controller (marketing sections, features) plus hardcoded structural copy from the Astro source.

- [ ] **Step 3: Verify in browser**

```bash
php artisan serve
```

Open `http://localhost:8000/station`. Verify all sections render, the workflow diagram SVG loads, and the waitlist form displays.

- [ ] **Step 4: Commit**

```bash
git add resources/views/pages/station.blade.php
git commit -m "feat: port Station product page to Blade"
```

---

## Task 6: Port tools pages

**Files:**
- Create: `resources/views/pages/tools/index.blade.php`, `resources/views/pages/tools/show.blade.php`, `resources/views/components/marketing-page.blade.php`, `resources/views/components/shellql-page.blade.php`
- Reference: `apps/site/src/pages/tools/index.astro`, `apps/site/src/pages/tools/[slug].astro`, `apps/site/src/components/MarketingPage.astro`, `apps/site/src/components/ShellQLPage.astro`

- [ ] **Step 1: Read the Astro tools pages**

Read:
- `apps/site/src/pages/tools/index.astro`
- `apps/site/src/pages/tools/[slug].astro`
- `apps/site/src/components/MarketingPage.astro`
- `apps/site/src/components/ShellQLPage.astro`

- [ ] **Step 2: Create tools index page**

Create `resources/views/pages/tools/index.blade.php` showing the two-section layout: API Tools (accord, drift, forge) and CLI/TUI Tools (shellframe, seed, ptyunit, shellql).

- [ ] **Step 3: Create generic tool page component**

Create `resources/views/components/marketing-page.blade.php` — the reusable template that renders name, tagline, install block, description, screenshots, features, code example, and marketing sections for any tool.

- [ ] **Step 4: Create ShellQL custom page component**

Create `resources/views/components/shellql-page.blade.php` — ShellQL gets its own layout with hero, install command, screenshots, features, and demo gif.

- [ ] **Step 5: Create tool show page**

Create `resources/views/pages/tools/show.blade.php`:

```blade
<x-layouts.marketing :title="$package['name']" :description="$package['tagline']">
    @if ($package['slug'] === 'shellql')
        <x-shellql-page :package="$package" />
    @else
        <x-marketing-page :package="$package" />
    @endif
</x-layouts.marketing>
```

- [ ] **Step 6: Verify in browser**

Check `/tools`, `/tools/accord`, `/tools/shellql`, `/tools/shellframe`. Verify:
- Index shows both sections with all tools
- Individual pages render install blocks, features, code examples
- ShellQL page shows screenshots and demo gif
- Links between pages work

- [ ] **Step 7: Commit**

```bash
git add resources/views/pages/tools/ resources/views/components/marketing-page.blade.php resources/views/components/shellql-page.blade.php
git commit -m "feat: port tools index and detail pages to Blade

Generic marketing page template + custom ShellQL layout.
Seven tool pages render from config/packages.php data."
```

---

## Task 7: Port coming-soon pages

**Files:**
- Create: `resources/views/pages/coming-soon.blade.php`, `resources/views/components/coming-soon-page.blade.php`
- Reference: `apps/site/src/components/ComingSoonPage.astro`, `apps/site/src/pages/guit/index.astro`, `apps/site/src/pages/sigil/index.astro`, `apps/site/src/pages/conduit/index.astro`

- [ ] **Step 1: Read the Astro coming-soon template**

Read `apps/site/src/components/ComingSoonPage.astro` and one of the product pages (e.g., `apps/site/src/pages/sigil/index.astro`) for the status note pattern.

- [ ] **Step 2: Create coming-soon component and page**

Create `resources/views/components/coming-soon-page.blade.php` and `resources/views/pages/coming-soon.blade.php`:

```blade
{{-- resources/views/pages/coming-soon.blade.php --}}
<x-layouts.marketing :title="$product['name']" :description="$product['tagline']">
    <x-coming-soon-page :product="$product" />
</x-layouts.marketing>
```

The component shows: "Coming Soon" label, product name, tagline, description, status note (if present), and waitlist form posting to Formspree.

- [ ] **Step 3: Verify in browser**

Check `/guit`, `/sigil`, `/conduit`. Each should render correctly with the waitlist form.

- [ ] **Step 4: Commit**

```bash
git add resources/views/pages/coming-soon.blade.php resources/views/components/coming-soon-page.blade.php
git commit -m "feat: port coming-soon product pages to Blade"
```

---

## Task 8: Migrate static assets

**Files:**
- Copy: All files from `apps/site/public/` to `public/`

- [ ] **Step 1: Copy assets**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev

# Favicons
cp apps/site/public/favicon.ico public/
cp apps/site/public/favicon.svg public/

# Station SVGs
mkdir -p public/station
cp apps/site/public/station/*.svg public/station/

# Screenshots
mkdir -p public/screenshots/shellql
cp apps/site/public/screenshots/shellql/*.png public/screenshots/shellql/

# ShellQL assets
mkdir -p public/shellql
cp apps/site/public/shellql/* public/shellql/
```

- [ ] **Step 2: Verify assets load**

```bash
php artisan serve &
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/favicon.svg
# Expected: 200
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/station/implementation-diagram.svg
# Expected: 200
kill %1
```

- [ ] **Step 3: Commit**

```bash
git add public/favicon.* public/station/ public/screenshots/ public/shellql/
git commit -m "feat: migrate static assets from Astro public directory"
```

---

## Task 9: Full parity verification

**Files:**
- No new files

- [ ] **Step 1: Build frontend assets**

```bash
npm run build
```

- [ ] **Step 2: Start the server and verify every route**

```bash
php artisan serve
```

Check each route in a browser:

| URL | Check |
|-----|-------|
| `/` | Hero, workflow animation, all sections, waitlist form |
| `/station` | Full product page, diagram SVG, features, waitlist |
| `/tools` | Both tool sections, all 7 tools listed |
| `/tools/accord` | Generic tool page renders |
| `/tools/shellql` | Custom ShellQL page, screenshots, demo gif |
| `/tools/shellframe` | Generic tool page |
| `/tools/seed` | Generic tool page |
| `/tools/ptyunit` | Generic tool page with marketing sections |
| `/tools/drift` | Generic tool page |
| `/tools/forge` | Generic tool page |
| `/guit` | Coming soon page with waitlist |
| `/sigil` | Coming soon page with status note |
| `/conduit` | Coming soon page with waitlist |
| `/accord` | 301 redirect to `/tools/accord` |
| `/drift` | 301 redirect to `/tools/drift` |

- [ ] **Step 3: Check responsive layout**

Resize browser to mobile width. Verify nav collapses, sections stack, forms remain usable.

- [ ] **Step 4: Commit any fixes**

```bash
git add -A
git commit -m "fix: parity adjustments after full route verification"
```

---

## Task 10: Clean up Astro artifacts

**Files:**
- Remove: `apps/site/`, `vercel.json`
- Modify: `.gitignore`

- [ ] **Step 1: Verify docs still builds standalone**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev/apps/docs
npm ci && npm run build
```

This must pass before removing anything — docs continuity is the gate.

- [ ] **Step 2: Remove Astro marketing site and Vercel config via git**

Use `git rm` so removals are tracked in the commit, not raw shell deletes:

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
git rm -r apps/site
git rm vercel.json
```

- [ ] **Step 3: Clean up .gitignore**

Remove Astro-specific entries (`.astro/`, `dist/`) from `.gitignore`.

- [ ] **Step 4: Commit**

```bash
git add .gitignore
git commit -m "chore: remove Astro marketing site after Laravel parity

apps/docs/ retained for separate docs deployment.
vercel.json removed — redirects now in routes/web.php."
```

---

## Task 11: Forge deployment preparation

**Files:**
- Create: `.forge/` deployment script (or use Forge UI)
- Modify: `.env.example`

- [ ] **Step 1: Update .env.example for production**

Add production-relevant defaults:

```env
APP_NAME="fissible"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://fissible.dev

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fissible
DB_USERNAME=fissible
DB_PASSWORD=

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Note: These are marketing-site-only defaults. When Station modules
# are integrated (Phase 2), upgrade to:
#   CACHE_STORE=redis
#   SESSION_DRIVER=redis
#   QUEUE_CONNECTION=redis
# and provision Redis on the Forge server.
```

- [ ] **Step 2: Document Forge setup steps**

The following steps are performed in the Forge UI (not automated):

1. **Create server** via Forge on a supported provider (e.g., DigitalOcean $6-12/mo, Hetzner $4-8/mo, or AWS Lightsail $5-10/mo — choose based on cost/region preference):
   - Ubuntu 24.04, PHP 8.4, MySQL 8, Nginx
   - Region: closest to primary audience
2. **Create site** in Forge:
   - Domain: `fissible.dev`
   - Project type: General PHP/Laravel
   - Web directory: `/public`
3. **Link GitHub repo**: `fissible/fissible.dev`, branch `main`
4. **Configure deploy script** in Forge:

```bash
cd /home/forge/fissible.dev

# Enter maintenance mode BEFORE risky operations
php artisan down --refresh=15

git pull origin $FORGE_SITE_BRANCH

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Back online only after everything succeeds
php artisan up
```

5. **Set environment variables** in Forge UI (copy from `.env.example`, fill secrets)
6. **Configure SSL** via Let's Encrypt in Forge
7. **Update DNS**: Point `fissible.dev` A record to Forge server IP
8. **Keep `docs.fissible.dev`** pointing to current Vercel/static deploy

- [ ] **Step 3: Commit**

```bash
git add .env.example
git commit -m "chore: update .env.example for Forge/production deployment"
```

---

## Task 12: Update PLAN.md and close issue

**Files:**
- Modify: `PLAN.md`

- [ ] **Step 1: Update PLAN.md with completion status**

Mark phases 0, 1, and 4 as complete. Phase 2 (Station-shaped app structure) and Phase 3 (docs coexistence) remain as future work.

- [ ] **Step 2: Comment on the GitHub issue**

```bash
gh issue comment 5 --repo fissible/fissible.dev --body "Laravel scaffold complete. Marketing site ported to Blade views. Forge deployment configured. Astro marketing app removed. Docs remain as separate Starlight deploy.

Remaining future work:
- Phase 2: Station module integration (Filament admin, content types, workflows)
- Phase 3: Docs migration decision (keep Starlight vs move into Station)"
```

- [ ] **Step 3: Close issue or leave open for Phase 2**

If the user considers scaffold + migration sufficient for this issue, close it. Otherwise leave open and create sub-issues for Phase 2/3.

---

## Notes

**Forge cost estimate:** A small VPS ($4-12/mo on DigitalOcean, Hetzner, or AWS Lightsail) is sufficient for a marketing site with low traffic. Choose provider based on cost/region preference. Scale up when Station gets real users.

**What this plan does NOT include:**
- Filament admin panel setup (Phase 2 — separate plan)
- Station module installation (Phase 2)
- CMS-managed content (Phase 2)
- Docs migration into Station (Phase 3)
- CI/CD pipeline (can use Forge auto-deploy on push)

**Dependency order:**
```
Task 1 (scaffold) → Task 2 (layout/CSS) → Task 3 (homepage)
                                         → Task 4 (controller/routes/data) → Task 5 (station page)
                                                                            → Task 6 (tools pages)
                                                                            → Task 7 (coming-soon)
                                         → Task 8 (assets)
Task 3-8 all done → Task 9 (parity check) → Task 10 (cleanup) → Task 11 (Forge) → Task 12 (close)
```
