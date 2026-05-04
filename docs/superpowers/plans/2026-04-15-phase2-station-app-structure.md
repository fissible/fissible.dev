# Phase 2: Station-Shaped App Structure — Implementation Plan

> **Superseded:** This plan describes the temporary `fissible.dev` Laravel shell. Current direction is to serve `fissible.dev` as a first-party tenant from the canonical Station app. See `docs/superpowers/plans/2026-04-15-station-dogfood-handoff.md`.

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make fissible.dev a working Station instance with Filament admin, tenant subdomain rendering, and auth.

**Architecture:** Filament v5 admin panel at `/admin` on the root domain with `is_platform_admin` gating. Tenant sites render on `{slug}.fissible.dev` subdomains via host-constrained routes and resolution middleware. Body content sanitized on output. StationServiceProvider registers service bindings.

**Tech Stack:** Laravel 13, Filament v5.5, PHPUnit, SQLite (test/dev), MySQL (prod)

**Spec:** `docs/superpowers/specs/2026-04-14-phase2-station-app-structure.md`

---

## File Structure

### New files

```
app/
  Console/Commands/MakeAdminCommand.php        — artisan station:make-admin
  Filament/Resources/
    TenantResource.php                         — Filament CRUD for Tenant
    TenantResource/Pages/ListTenants.php
    TenantResource/Pages/CreateTenant.php
    TenantResource/Pages/EditTenant.php
    TenantPageResource.php                     — Filament CRUD for TenantPage
    TenantPageResource/Pages/ListTenantPages.php
    TenantPageResource/Pages/CreateTenantPage.php
    TenantPageResource/Pages/EditTenantPage.php
    TenantMenuResource.php                     — Filament CRUD for TenantMenu
    TenantMenuResource/Pages/ListTenantMenus.php
    TenantMenuResource/Pages/CreateTenantMenu.php
    TenantMenuResource/Pages/EditTenantMenu.php
  Http/
    Controllers/TenantSiteController.php       — public tenant page rendering
    Middleware/ResolveTenant.php                — subdomain → tenant binding
  Providers/
    Filament/AdminPanelProvider.php             — Filament panel config
    StationServiceProvider.php                  — service bindings + tenant() helper
database/migrations/
  xxxx_add_is_platform_admin_to_users_table.php
resources/views/
  tenant/layout.blade.php                      — tenant site base layout
  tenant/page.blade.php                        — tenant page template
routes/
  tenant.php                                   — tenant subdomain routes
tests/Feature/
  MakeAdminCommandTest.php
  TenantResolutionTest.php
  TenantSiteTest.php
  FilamentAdminAccessTest.php
```

### Modified files

```
app/Models/User.php                            — add is_platform_admin, FilamentUser
bootstrap/app.php                              — load tenant.php, register middleware alias
bootstrap/providers.php                        — add StationServiceProvider, AdminPanelProvider
config/station.php                             — add app_hosts to domains
routes/web.php                                 — remove /platform routes
```

### Removed files

```
app/Http/Controllers/Platform/TenantController.php
app/Http/Middleware/EnsurePlatformEnabled.php
resources/views/platform/tenants/index.blade.php
resources/views/platform/tenants/create.blade.php
resources/views/platform/tenants/show.blade.php
resources/views/components/layouts/platform.blade.php
```

---

## Task 1: Install Filament and add is_platform_admin migration

**Files:**
- Modify: `composer.json`
- Create: `database/migrations/xxxx_add_is_platform_admin_to_users_table.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Install Filament**

```bash
composer require filament/filament:"^5.5"
```

- [ ] **Step 2: Run Filament install**

```bash
php artisan filament:install --panels
```

This creates `app/Providers/Filament/AdminPanelProvider.php` and publishes assets.

- [ ] **Step 3: Create is_platform_admin migration**

```bash
php artisan make:migration add_is_platform_admin_to_users_table --table=users
```

Edit the generated migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_platform_admin')->default(false)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_platform_admin');
        });
    }
};
```

- [ ] **Step 4: Run migrations**

```bash
php artisan migrate
```

Expected: migration runs successfully, `is_platform_admin` column added.

- [ ] **Step 5: Update User model**

Update `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_platform_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_platform_admin;
    }
}
```

- [ ] **Step 6: Configure AdminPanelProvider**

Edit `app/Providers/Filament/AdminPanelProvider.php`. After the `filament:install` scaffold, modify the panel configuration:

```php
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';

        return $panel
            ->default()
            ->id('admin')
            ->domain($domain)
            ->path('admin')
            ->login()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
```

Key: `->domain($domain)` constrains admin to the root domain only, preventing access on tenant subdomains.

- [ ] **Step 7: Verify Filament serves**

```bash
php artisan serve --port=8200 &
sleep 1
curl -s -o /dev/null -w "%{http_code}" http://localhost:8200/admin/login
kill %1
```

Expected: `200` (Filament login page renders).

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: install Filament v5, add is_platform_admin to users

Filament admin panel at /admin, domain-constrained to root host.
Only users with is_platform_admin=true can access the panel.

Refs: Phase 2"
```

---

## Task 2: Create station:make-admin command

**Files:**
- Create: `app/Console/Commands/MakeAdminCommand.php`
- Create: `tests/Feature/MakeAdminCommandTest.php`

- [ ] **Step 1: Write the test**

Create `tests/Feature/MakeAdminCommandTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_new_admin_user(): void
    {
        $this->artisan('station:make-admin', ['email' => 'admin@fissible.dev'])
            ->expectsQuestion('Password for new user', 'secret-password')
            ->expectsQuestion('Name', 'Admin')
            ->assertExitCode(0);

        $user = User::where('email', 'admin@fissible.dev')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_platform_admin);
        $this->assertEquals('Admin', $user->name);
    }

    public function test_promotes_existing_user(): void
    {
        User::factory()->create([
            'email' => 'existing@fissible.dev',
            'is_platform_admin' => false,
        ]);

        $this->artisan('station:make-admin', ['email' => 'existing@fissible.dev'])
            ->assertExitCode(0);

        $user = User::where('email', 'existing@fissible.dev')->first();
        $this->assertTrue($user->is_platform_admin);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --filter=MakeAdminCommandTest
```

Expected: FAIL — command not found.

- [ ] **Step 3: Create the command**

Create `app/Console/Commands/MakeAdminCommand.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminCommand extends Command
{
    protected $signature = 'station:make-admin {email : The email address}';

    protected $description = 'Create or promote a user to platform admin';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['is_platform_admin' => true]);
            $this->info("User {$email} promoted to platform admin.");

            return self::SUCCESS;
        }

        $password = $this->secret('Password for new user');
        $name = $this->ask('Name', 'Admin');

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_platform_admin' => true,
        ]);

        $this->info("Admin user {$email} created.");

        return self::SUCCESS;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --filter=MakeAdminCommandTest
```

Expected: 2 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Console/Commands/MakeAdminCommand.php tests/Feature/MakeAdminCommandTest.php
git commit -m "feat: add station:make-admin artisan command

Creates a new admin user or promotes an existing one.
Used for first-deploy bootstrap."
```

---

## Task 3: Create StationServiceProvider

**Files:**
- Create: `app/Providers/StationServiceProvider.php`
- Modify: `bootstrap/providers.php`

- [ ] **Step 1: Create the provider**

Create `app/Providers/StationServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Services\TenantDomainService;
use App\Services\TenantLifecycleService;
use App\Services\TenantProvisioner;
use App\Services\TenantSeeder;
use Illuminate\Support\ServiceProvider;

class StationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantDomainService::class);
        $this->app->singleton(TenantLifecycleService::class);
        $this->app->singleton(TenantSeeder::class);
        $this->app->singleton(TenantProvisioner::class);
    }

    public function boot(): void
    {
        //
    }
}
```

- [ ] **Step 2: Register the provider**

Edit `bootstrap/providers.php`:

```php
<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\StationServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    StationServiceProvider::class,
];
```

- [ ] **Step 3: Add tenant() helper**

Add a global helper. Create `app/helpers.php`:

```php
<?php

use App\Models\Tenant;

if (! function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        return app()->bound('tenant') ? app('tenant') : null;
    }
}
```

Add autoload entry in `composer.json` — add to the `autoload.files` array:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/helpers.php"
    ]
},
```

Then:

```bash
composer dump-autoload
```

- [ ] **Step 4: Verify services resolve**

```bash
php artisan tinker --execute="echo get_class(app(App\Services\TenantProvisioner::class));"
```

Expected: `App\Services\TenantProvisioner`

- [ ] **Step 5: Commit**

```bash
git add app/Providers/StationServiceProvider.php app/helpers.php bootstrap/providers.php composer.json
git commit -m "feat: add StationServiceProvider and tenant() helper

Registers service singletons. Adds tenant() global helper
for accessing the current tenant from the request."
```

---

## Task 4: Create Filament resources and remove old platform code

**Files:**
- Create: `app/Filament/Resources/TenantResource.php` + Pages/
- Create: `app/Filament/Resources/TenantPageResource.php` + Pages/
- Create: `app/Filament/Resources/TenantMenuResource.php` + Pages/
- Remove: `app/Http/Controllers/Platform/TenantController.php`
- Remove: `app/Http/Middleware/EnsurePlatformEnabled.php`
- Remove: `resources/views/platform/` (3 files)
- Remove: `resources/views/components/layouts/platform.blade.php`
- Modify: `routes/web.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Generate Filament resources**

```bash
php artisan make:filament-resource Tenant --generate
php artisan make:filament-resource TenantPage --generate
php artisan make:filament-resource TenantMenu --generate
```

The `--generate` flag auto-generates form fields and table columns from the model's fillable attributes and database schema.

- [ ] **Step 2: Customize TenantResource form**

Edit `app/Filament/Resources/TenantResource.php`. Replace the generated `form()` method:

```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('slug')
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true),
        Forms\Components\TextInput::make('domain')
            ->maxLength(255)
            ->unique(ignoreRecord: true),
        Forms\Components\Select::make('status')
            ->options([
                'active' => 'Active',
                'suspended' => 'Suspended',
                'archived' => 'Archived',
            ])
            ->default('active')
            ->required(),
        Forms\Components\Toggle::make('is_demo')
            ->default(false),
        Forms\Components\DateTimePicker::make('demo_expires_at'),
    ]);
}
```

Replace the `table()` method:

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->searchable(),
            Tables\Columns\TextColumn::make('status')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'suspended' => 'warning',
                    'archived' => 'danger',
                    default => 'gray',
                }),
            Tables\Columns\IconColumn::make('is_demo')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->defaultSort('created_at', 'desc');
}
```

- [ ] **Step 3: Customize TenantPageResource form**

Edit `app/Filament/Resources/TenantPageResource.php`. Replace the `form()` method:

```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Select::make('tenant_id')
            ->relationship('tenant', 'name')
            ->required()
            ->searchable(),
        Forms\Components\TextInput::make('title')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('slug')
            ->required()
            ->maxLength(255),
        Forms\Components\Textarea::make('excerpt')
            ->rows(2),
        Forms\Components\RichEditor::make('body')
            ->columnSpanFull(),
        Forms\Components\Toggle::make('is_homepage')
            ->default(false),
        Forms\Components\Toggle::make('is_system')
            ->default(false),
        Forms\Components\DateTimePicker::make('published_at'),
    ]);
}
```

Replace the `table()` method:

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('tenant.name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('slug'),
            Tables\Columns\IconColumn::make('is_homepage')->boolean(),
            Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
        ])
        ->defaultSort('updated_at', 'desc');
}
```

- [ ] **Step 4: Customize TenantMenuResource form**

Edit `app/Filament/Resources/TenantMenuResource.php`. Replace the `form()` method:

```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Select::make('tenant_id')
            ->relationship('tenant', 'name')
            ->required()
            ->searchable(),
        Forms\Components\TextInput::make('location')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('label')
            ->required()
            ->maxLength(255),
        Forms\Components\KeyValue::make('items')
            ->keyLabel('Label')
            ->valueLabel('URL')
            ->columnSpanFull(),
    ]);
}
```

Replace the `table()` method:

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('tenant.name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('location'),
            Tables\Columns\TextColumn::make('label'),
        ])
        ->defaultSort('tenant_id');
}
```

- [ ] **Step 5: Remove old platform code**

```bash
git rm app/Http/Controllers/Platform/TenantController.php
git rm app/Http/Middleware/EnsurePlatformEnabled.php
git rm -r resources/views/platform/
git rm resources/views/components/layouts/platform.blade.php
```

- [ ] **Step 6: Update routes/web.php**

Remove the `use` import for `TenantController` and the entire `/platform` route group. The file becomes:

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

// Redirects — only for tools that have pages
Route::permanentRedirect('/accord', '/tools/accord');
Route::permanentRedirect('/drift', '/tools/drift');
Route::permanentRedirect('/forge', '/tools/forge');
Route::permanentRedirect('/seed', '/tools/seed');
Route::permanentRedirect('/shellframe', '/tools/shellframe');
Route::permanentRedirect('/ptyunit', '/tools/ptyunit');
Route::permanentRedirect('/shellql', '/tools/shellql');

// Deprecated modules — redirect to tools index
Route::permanentRedirect('/watch', '/tools');
Route::permanentRedirect('/fault', '/tools');
```

- [ ] **Step 7: Update bootstrap/app.php**

Remove the `platform.enabled` middleware alias (EnsurePlatformEnabled is deleted):

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

- [ ] **Step 8: Write test for admin access control**

Create `tests/Feature/FilamentAdminAccessTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_panel(): void
    {
        $user = User::factory()->create(['is_platform_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_panel(): void
    {
        $user = User::factory()->create(['is_platform_admin' => true]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }
}
```

- [ ] **Step 9: Update UserFactory for is_platform_admin**

Edit `database/factories/UserFactory.php` — add `is_platform_admin` to the definition:

```php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => static::$password ??= Hash::make('password'),
        'remember_token' => Str::random(10),
        'is_platform_admin' => false,
    ];
}
```

- [ ] **Step 10: Run tests**

```bash
php artisan test --filter=FilamentAdminAccessTest
php artisan test --filter=MakeAdminCommandTest
```

Expected: all tests pass.

- [ ] **Step 11: Verify route list**

```bash
php artisan route:list
```

Expected: no `/platform/*` routes. `/admin` routes present from Filament.

- [ ] **Step 12: Commit**

```bash
git add -A
git commit -m "feat: add Filament resources, remove old platform controller

TenantResource, TenantPageResource, TenantMenuResource replace
the raw Blade platform UI. Old controller, middleware, and views removed.
Admin access gated by is_platform_admin."
```

---

## Task 5: Add tenant resolution middleware and config

**Files:**
- Create: `app/Http/Middleware/ResolveTenant.php`
- Create: `tests/Feature/TenantResolutionTest.php`
- Modify: `config/station.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Update config/station.php**

Add `app_hosts` to the `domains` section:

```php
'domains' => [
    'managed_root' => env('STATION_MANAGED_ROOT_DOMAIN', 'fissible.dev'),
    'app_hosts' => [
        env('STATION_PLATFORM_HOST', 'platform.fissible.dev'),
    ],
    'reserved_slugs' => [
        'www',
        'platform',
        'station-demo',
        'api',
        'admin',
        'mail',
        'staging',
        'app',
        'support',
    ],
],
```

- [ ] **Step 2: Write the test**

Create `tests/Feature/TenantResolutionTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolveTenant;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TenantResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function resolveFromHost(string $host): mixed
    {
        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_HOST' => $host,
        ]);

        $middleware = new ResolveTenant;
        $result = null;

        $middleware->handle($request, function ($req) use (&$result) {
            $result = $req->attributes->get('tenant');
            return response('ok');
        });

        return $result;
    }

    public function test_resolves_active_tenant_from_subdomain(): void
    {
        $tenant = Tenant::factory()->create([
            'slug' => 'acme',
            'status' => 'active',
        ]);

        config(['station.domains.managed_root' => 'fissible.dev']);

        $resolved = $this->resolveFromHost('acme.fissible.dev');

        $this->assertNotNull($resolved);
        $this->assertEquals($tenant->id, $resolved->id);
    }

    public function test_skips_root_domain(): void
    {
        config(['station.domains.managed_root' => 'fissible.dev']);

        $resolved = $this->resolveFromHost('fissible.dev');

        $this->assertNull($resolved);
    }

    public function test_skips_www(): void
    {
        config(['station.domains.managed_root' => 'fissible.dev']);

        $resolved = $this->resolveFromHost('www.fissible.dev');

        $this->assertNull($resolved);
    }

    public function test_skips_app_hosts(): void
    {
        config([
            'station.domains.managed_root' => 'fissible.dev',
            'station.domains.app_hosts' => ['platform.fissible.dev'],
        ]);

        $resolved = $this->resolveFromHost('platform.fissible.dev');

        $this->assertNull($resolved);
    }

    public function test_skips_localhost(): void
    {
        config(['station.domains.managed_root' => 'fissible.dev']);

        $resolved = $this->resolveFromHost('localhost');

        $this->assertNull($resolved);
    }

    public function test_404s_for_unknown_slug(): void
    {
        config(['station.domains.managed_root' => 'fissible.dev']);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->resolveFromHost('nonexistent.fissible.dev');
    }

    public function test_404s_for_suspended_tenant(): void
    {
        Tenant::factory()->create([
            'slug' => 'suspended-co',
            'status' => 'suspended',
        ]);

        config(['station.domains.managed_root' => 'fissible.dev']);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->resolveFromHost('suspended-co.fissible.dev');
    }
}
```

- [ ] **Step 3: Create Tenant factory**

Create `database/factories/TenantFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => 'active',
            'is_demo' => false,
        ];
    }
}
```

- [ ] **Step 4: Run test to verify it fails**

```bash
php artisan test --filter=TenantResolutionTest
```

Expected: FAIL — ResolveTenant class not found.

- [ ] **Step 5: Create the middleware**

Create `app/Http/Middleware/ResolveTenant.php`:

```php
<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if ($this->shouldSkip($host)) {
            return $next($request);
        }

        $managedRoot = config('station.domains.managed_root');
        $subdomain = str_replace('.' . $managedRoot, '', $host);

        if ($subdomain === $host || $subdomain === '') {
            return $next($request);
        }

        $tenant = Tenant::where('slug', $subdomain)
            ->where('status', 'active')
            ->first();

        abort_unless($tenant, 404);

        app()->instance('tenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }

    protected function shouldSkip(string $host): bool
    {
        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return true;
        }

        $managedRoot = config('station.domains.managed_root');

        if ($host === $managedRoot || $host === 'www.' . $managedRoot) {
            return true;
        }

        $appHosts = config('station.domains.app_hosts', []);

        return in_array($host, $appHosts, true);
    }
}
```

- [ ] **Step 6: Register middleware alias in bootstrap/app.php**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant.resolve' => \App\Http\Middleware\ResolveTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

- [ ] **Step 7: Run tests**

```bash
php artisan test --filter=TenantResolutionTest
```

Expected: all 7 tests pass.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Middleware/ResolveTenant.php config/station.php bootstrap/app.php database/factories/TenantFactory.php tests/Feature/TenantResolutionTest.php
git commit -m "feat: add ResolveTenant middleware with configurable skip-list

Resolves tenant from subdomain, binds to app container.
Skips root domain, www, localhost, and configured app_hosts.
404s for unknown or inactive tenants."
```

---

## Task 6: Add tenant site rendering

**Files:**
- Create: `app/Http/Controllers/TenantSiteController.php`
- Create: `resources/views/tenant/layout.blade.php`
- Create: `resources/views/tenant/page.blade.php`
- Create: `routes/tenant.php`
- Create: `tests/Feature/TenantSiteTest.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Write the test**

Create `tests/Feature/TenantSiteTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantMenu;
use App\Models\TenantPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['station.domains.managed_root' => 'fissible.dev']);
    }

    public function test_tenant_homepage_renders(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Welcome to Acme',
            'slug' => 'home',
            'body' => '<p>Hello world</p>',
            'is_homepage' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/', ['HTTP_HOST' => 'acme.fissible.dev']);

        $response->assertOk();
        $response->assertSee('Welcome to Acme');
        $response->assertSee('Hello world');
    }

    public function test_tenant_page_by_slug_renders(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'About Us',
            'slug' => 'about',
            'body' => '<p>About content</p>',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/about', ['HTTP_HOST' => 'acme.fissible.dev']);

        $response->assertOk();
        $response->assertSee('About Us');
    }

    public function test_unpublished_page_returns_404(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get('/draft', ['HTTP_HOST' => 'acme.fissible.dev']);

        $response->assertNotFound();
    }

    public function test_future_published_page_returns_404(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'future',
            'published_at' => now()->addWeek(),
        ]);

        $response = $this->get('/future', ['HTTP_HOST' => 'acme.fissible.dev']);

        $response->assertNotFound();
    }

    public function test_body_is_sanitized(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme', 'status' => 'active']);
        TenantPage::factory()->create([
            'tenant_id' => $tenant->id,
            'slug' => 'xss',
            'body' => '<p>Safe</p><script>alert("xss")</script>',
            'is_homepage' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/', ['HTTP_HOST' => 'acme.fissible.dev']);

        $response->assertOk();
        $response->assertSee('Safe');
        $response->assertDontSee('<script>');
    }

    public function test_unknown_tenant_returns_404(): void
    {
        $response = $this->get('/', ['HTTP_HOST' => 'nonexistent.fissible.dev']);

        $response->assertNotFound();
    }
}
```

- [ ] **Step 2: Create TenantPage factory**

Create `database/factories/TenantPageFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantPageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'slug' => fake()->slug(2),
            'title' => fake()->sentence(4),
            'excerpt' => fake()->sentence(),
            'body' => '<p>' . fake()->paragraph() . '</p>',
            'is_homepage' => false,
            'is_system' => false,
            'published_at' => now(),
        ];
    }
}
```

- [ ] **Step 3: Run test to verify it fails**

```bash
php artisan test --filter=TenantSiteTest
```

Expected: FAIL — controller/routes not found.

- [ ] **Step 4: Create the controller**

Create `app/Http/Controllers/TenantSiteController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\TenantPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantSiteController extends Controller
{
    public function home(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $page = TenantPage::where('tenant_id', $tenant->id)
            ->where('is_homepage', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        abort_unless($page, 404);

        return view('tenant.page', [
            'tenant' => $tenant,
            'page' => $page,
            'body' => Str::sanitizeHtml($page->body),
            'menus' => $tenant->menus->groupBy('location'),
        ]);
    }

    public function page(Request $request, string $tenantSlug, string $slug)
    {
        $tenant = $request->attributes->get('tenant');

        $page = TenantPage::where('tenant_id', $tenant->id)
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        abort_unless($page, 404);

        return view('tenant.page', [
            'tenant' => $tenant,
            'page' => $page,
            'body' => Str::sanitizeHtml($page->body),
            'menus' => $tenant->menus->groupBy('location'),
        ]);
    }
}
```

Note: The `page()` method receives `$tenantSlug` as the first parameter because the route domain parameter `{tenantSlug}` is passed as a route parameter before `{slug}`.

- [ ] **Step 5: Create tenant views**

Create `resources/views/tenant/layout.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} — {{ $tenant->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <nav class="nav">
        <a href="/" class="nav-logo">{{ $tenant->name }}</a>
        @if(isset($menus['primary']) && $menus['primary']->first())
            <div class="nav-links">
                @foreach($menus['primary']->first()->items ?? [] as $label => $url)
                    <a href="{{ $url }}" class="nav-link">{{ $label }}</a>
                @endforeach
            </div>
        @endif
    </nav>
    <main>
        {{ $slot }}
    </main>
</body>
</html>
```

Create `resources/views/tenant/page.blade.php`:

```blade
<x-tenant.layout :tenant="$tenant" :page="$page" :menus="$menus">
    <article class="section">
        <div class="section-inner">
            <h1>{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="marketing-tagline">{{ $page->excerpt }}</p>
            @endif
            <div class="tenant-body">
                {!! $body !!}
            </div>
        </div>
    </article>
</x-tenant.layout>
```

Move the layout to the components directory so Blade's anonymous component resolution works:

```bash
mkdir -p resources/views/components/tenant
mv resources/views/tenant/layout.blade.php resources/views/components/tenant/layout.blade.php
```

- [ ] **Step 6: Create tenant routes**

Create `routes/tenant.php`:

```php
<?php

use App\Http\Controllers\TenantSiteController;
use Illuminate\Support\Facades\Route;

Route::domain('{tenantSlug}.' . config('station.domains.managed_root'))
    ->middleware(['web', 'tenant.resolve'])
    ->group(function () {
        Route::get('/', [TenantSiteController::class, 'home']);
        Route::get('/{slug}', [TenantSiteController::class, 'page'])
            ->where('slug', '[a-z0-9\-]+');
    });
```

- [ ] **Step 7: Load tenant routes in bootstrap/app.php**

Update `bootstrap/app.php` to load `routes/tenant.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant.resolve' => \App\Http\Middleware\ResolveTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

Add the `use` import at the top:

```php
use Illuminate\Support\Facades\Route;
```

- [ ] **Step 8: Run tests**

```bash
php artisan test --filter=TenantSiteTest
```

Expected: all 6 tests pass.

- [ ] **Step 9: Run full test suite**

```bash
php artisan test
```

Expected: all tests pass (MakeAdminCommand, FilamentAdminAccess, TenantResolution, TenantSite).

- [ ] **Step 10: Commit**

```bash
git add -A
git commit -m "feat: add tenant site rendering on subdomains

TenantSiteController renders pages at {slug}.fissible.dev.
Body HTML sanitized via Str::sanitizeHtml before output.
Host-constrained routes in routes/tenant.php.
Tenant layout reuses marketing CSS design system."
```

---

## Task 7: Update deploy script and .env.example

**Files:**
- Modify: `.env.example`
- Modify: `forge-deploy.sh`

- [ ] **Step 1: Update .env.example**

Add Filament-relevant notes:

```
# After first deploy, run:
#   php artisan station:make-admin your@email.com
#   php artisan storage:link
```

Ensure `STATION_PLATFORM_ENABLED` is still present (used by config, even though
the middleware is removed — the config key may be read by future code).

- [ ] **Step 2: Update forge-deploy.sh first-deploy comments**

Add to the first-deploy checklist:

```bash
#   5. Create admin user: php artisan station:make-admin admin@fissible.dev
#   6. Publish Filament assets: php artisan filament:assets
```

Add `php artisan filament:assets` to the deploy script body (after `npm run build`):

```bash
npm ci --ignore-scripts
npm run build

php artisan filament:assets
```

- [ ] **Step 3: Commit**

```bash
git add .env.example forge-deploy.sh
git commit -m "chore: update deploy script for Filament and admin bootstrap"
```

---

## Dependency Order

```
Task 1 (Filament + migration) → Task 2 (make-admin command)
                               → Task 3 (StationServiceProvider)
                               → Task 4 (Filament resources + remove platform)
                                   → Task 5 (ResolveTenant middleware)
                                       → Task 6 (Tenant site rendering)
                                           → Task 7 (Deploy updates)
```

All tasks are sequential — each depends on the previous.
