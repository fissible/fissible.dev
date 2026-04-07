// apps/site/src/data/packages.ts

export interface MarketingSection {
  title: string;
  body: string;
  code?: string;
}

export interface OssPackage {
  slug: string;
  name: string;
  tagline: string;
  description?: string;
  install: string;
  installLabel: 'brew' | 'composer';
  installNote?: string;
  docsUrl: string;
  githubUrl: string;
  features: [string, string, string, string];
  codeExample: string;
  screenshotsLabel?: string;
  screenshots?: string[];
  suite: 'tui' | 'php';
  marketingSections?: MarketingSection[];
}

export interface PaidProduct {
  slug: string;
  name: string;
  tagline: string;
  description: string;
  status: 'coming-soon' | 'purchase-pending';
  formspreeId: string;
  features?: Array<{ title: string; body: string }>;
  marketingSections?: MarketingSection[];
  screenshots?: Array<{ src: string; caption: string }>;
  screenshotsLabel?: string;
  pricingNote?: string;
}

export const tuiPackages: OssPackage[] = [
  {
    slug: 'shellframe',
    name: 'shellframe',
    tagline: 'TUI framework for bash',
    install: 'brew install fissible/tap/shellframe',
    installLabel: 'brew',
    docsUrl: 'https://docs.fissible.dev/shellframe',
    githubUrl: 'https://github.com/fissible/shellframe',
    suite: 'tui',
    features: [
      'Full widget set: list, grid, editor, tree, modal, tab-bar, input-field, app shell',
      'Composable shellframe_shell runtime with dirty-region rendering',
      '1000+ tests across bash 3.2–5.x',
      'Split-pane layout, diff-view, and synchronized scrolling',
    ],
    codeExample: `source shellframe.sh

shellframe_list_init items
shellframe_list_add items "Option A"
shellframe_list_add items "Option B"
shellframe_shell run`,
  },
  {
    slug: 'seed',
    name: 'seed',
    tagline: 'Bash fake data generator',
    install: 'brew install fissible/tap/seed',
    installLabel: 'brew',
    docsUrl: 'https://docs.fissible.dev/seed',
    githubUrl: 'https://github.com/fissible/seed',
    suite: 'tui',
    features: [
      '31 generators across scalar, record, ecommerce, CRM, and TUI categories',
      '4 output formats: JSON, CSV, SQL, key-value',
      'No runtime, no package manager — bash and awk only',
      'MCP server for AI-assisted test data generation',
    ],
    codeExample: `# Generate 10 user records as JSON
seed record.user --count 10 --format json

# Generate a CSV of 100 products
seed ecommerce.product --count 100 --format csv`,
  },
  {
    slug: 'ptyunit',
    name: 'ptyunit',
    tagline: 'PTY test framework for bash',
    description: 'Test your bash scripts — even the interactive ones that take over the terminal. ptyunit is the only bash test framework with real pseudoterminal support, built-in mocking, native code coverage, and zero dependencies.',
    install: 'brew install fissible/tap/ptyunit',
    installLabel: 'brew',
    docsUrl: 'https://docs.fissible.dev/ptyunit',
    githubUrl: 'https://github.com/fissible/ptyunit',
    suite: 'tui',
    features: [
      '221 assertions for terminal and PTY output testing',
      '15× faster than bats-core for PTY-dependent test suites',
      'Used by shellframe, shellql, and seed',
      'Bash 3.2–5.x compatible',
    ],
    codeExample: `source ptyunit.sh

pty_test "shows welcome screen" <<'EOF'
  pty_run "shql"
  pty_assert_contains "Welcome to shellql"
EOF`,
    marketingSections: [
      {
        title: 'Most test frameworks stop at stdout',
        body: 'If your script opens /dev/tty for a menu, a prompt, or a TUI — standard test tools can\'t touch it. ptyunit runs your script inside a real pseudoterminal, sends keystrokes like a human, strips ANSI escape codes, and gives you clean text to assert against. Menus, password prompts, progress bars, fzf pickers, dialog boxes — all testable.',
        code: `# Drive a TUI with keystrokes, assert on the result
out=$(python3 pty_run.py my_menu.sh DOWN DOWN ENTER)
assert_contains "$out" "You selected: cherry"`,
      },
      {
        title: 'Real bugs in real code',
        body: 'We pointed ptyunit at a 1,000-line bash TUI password generator posted on Reddit — no tests, two operating modes, keyboard navigation, history tracking, and i18n. ptyunit found a silent flag-ordering bug where -d (DB-safe) and -s (simple) conflict depending on order — passwords were labeled "DB-safe" but contained no DB-safe characters. It caught a crash when -l is passed without a value, and a bash 4.3 compatibility issue in the history code that only manifests interactively. None of the TUI behavior — mode toggles, history navigation, the help screen, language switching via arrow keys — is reachable from any other test framework.',
        code: `test_that "DB-safe flag wins regardless of flag order"
run passgen -d -s -l 24 -q
assert_match '[_.\\-]' "$output"

test_that "history navigation shows correct position"
out=$(python3 pty_run.py passgen r r r '<' q)
assert_contains "$out" "2/3"`,
      },
      {
        title: 'Mocking with zero boilerplate',
        body: 'Replace any command with a fake. Record every call. Verify arguments and call count. Mocks clean up automatically at the next test boundary — no manual teardown. Need smarter behavior? Use a heredoc body to write conditional logic.',
        code: `test_that "deploy pushes to staging"
ptyunit_mock git --output "pushed"
ptyunit_mock curl --exit 0

deploy_to_staging

assert_called git
assert_called_with git "push" "origin" "staging"
assert_called_times curl 1`,
      },
      {
        title: 'Coverage that works everywhere',
        body: 'Built-in code coverage via PS4 tracing — no kcov, no Linux-only tools. Works on macOS. PTY integration tests contribute to coverage automatically: TUI code paths exercised through the PTY driver appear in the report with no extra configuration. Set a minimum threshold to gate CI.',
      },
    ],
  },
  {
    slug: 'shellql',
    name: 'shellql',
    tagline: 'SQLite workbench that runs where your data lives',
    description: `You SSH into a server. The SQLite database is right there. Every GUI tool you own stops working.\n\nShellQL is a full SQLite workbench that runs in your terminal. Open a database on any server you can SSH into. Browse schemas, run queries, and edit records without leaving the shell. Runs on bash 3.2–5.2 across macOS and Linux, tested in CI.`,
    install: 'brew install fissible/tap/shellql',
    installLabel: 'brew',
    installNote: 'Works over SSH. No GUI. No port forwarding.',
    docsUrl: 'https://docs.fissible.dev/shellql',
    githubUrl: 'https://github.com/fissible/shellql',
    suite: 'tui',
    features: [
      'Schema browser, table view, query tab, and record inspector',
      'Full CRUD — insert, update, and delete rows with an intuitive UI',
      'Works over SSH — browse and edit data directly on the machine',
      'Mouse and keyboard support; bash 3.2–5.2, tested in CI across macOS and Linux',
    ],
    codeExample: `# Open a database directly
shql myapp.db

# Use a named connection from sigil
shql --connection production`,
    screenshotsLabel: 'Schema browser · Table view · Record editor',
    screenshots: [
      '/screenshots/shellql/schema-browser.png',
      '/screenshots/shellql/table-data-view.png',
      '/screenshots/shellql/row-editor.png',
    ],
    marketingSections: [
      {
        title: 'Works where your data lives',
        body: 'Remote servers. Docker containers. CI jobs. If the machine has bash and sqlite3, you can open the database directly. No GUI install. No port forwarding. No syncing files back to your laptop. SSH in and run it.',
        code: `# Local
shql myapp.db

# Remote — SSH and run directly
ssh user@server
shql /var/app/production.db

# Named connection from sigil
shql --connection production`,
      },
      {
        title: 'Browse, query, and mutate',
        body: 'ShellQL has four screens: a schema browser for listing all tables and views, a table view with inline filtering and multi-tab support (open several tables at once), a query tab for raw SQL execution, and a record editor — a schema-aware form overlay that shows column types and constraints. Insert, update, and delete are first-class. Table creation uses a SQL tab preloaded with a CREATE TABLE template, giving you full DDL control.',
      },
      {
        title: 'Keyboard-driven, mouse-friendly',
        body: 'Most terminal tools pick one input model. ShellQL supports both. Navigate screens and records with keyboard shortcuts for speed, or use the mouse when that\'s faster. Keybindings are shown at the bottom of each screen — you don\'t need to read the docs to get started.',
      },
      {
        title: 'Zero dependencies, real architecture',
        body: 'No runtime dependencies beyond bash and sqlite3 — both available on most systems. Built on shellframe, a TUI framework for bash that provides screen management, keyboard routing, and component lifecycle. Tested on bash 3.2–5.2 across macOS and Linux in CI.',
        code: `brew install fissible/tap/shellql`,
      },
    ],
  },
];

export const phpPackages: OssPackage[] = [
  {
    slug: 'accord',
    name: 'accord',
    tagline: 'OpenAPI contract validator for Laravel',
    install: 'composer require fissible/accord',
    installLabel: 'composer',
    docsUrl: 'https://docs.fissible.dev/accord',
    githubUrl: 'https://github.com/fissible/accord',
    suite: 'php',
    features: [
      'Runtime middleware validates requests and responses against your OpenAPI spec',
      'PSR-7/15 + Laravel driver — works with any PSR-15 stack',
      'OpenAPI 3.0 support',
      'Zero configuration for standard Laravel routes',
    ],
    codeExample: `// config/accord.php
return [
    'spec'               => base_path('openapi.yaml'),
    'validate_requests'  => true,
    'validate_responses' => true,
];`,
  },
  {
    slug: 'drift',
    name: 'drift',
    tagline: 'API drift detection and changelog generation',
    install: 'composer require fissible/drift',
    installLabel: 'composer',
    docsUrl: 'https://docs.fissible.dev/drift',
    githubUrl: 'https://github.com/fissible/drift',
    suite: 'php',
    features: [
      'Detects breaking and non-breaking changes between OpenAPI specs',
      'Semver impact analysis: major, minor, or patch bump',
      'Changelog generation from spec diffs',
      'Artisan commands: accord:version, drift:changelog',
    ],
    codeExample: `# Detect changes and suggest version bump
php artisan accord:version

# Generate changelog from spec diff
php artisan drift:changelog`,
  },
  {
    slug: 'forge',
    name: 'forge',
    tagline: 'OpenAPI spec scaffolding from Laravel routes',
    install: 'composer require fissible/forge',
    installLabel: 'composer',
    docsUrl: 'https://docs.fissible.dev/forge',
    githubUrl: 'https://github.com/fissible/forge',
    suite: 'php',
    features: [
      'Generates OpenAPI 3.0 specs from your existing Laravel routes',
      'Reads FormRequest validation rules to populate request schemas',
      'Artisan command: accord:generate',
      'Augments an existing spec or generates from scratch',
    ],
    codeExample: `# Generate openapi.yaml from your routes
php artisan forge:generate --output openapi.yaml`,
  },
  {
    slug: 'watch',
    name: 'watch',
    tagline: 'Browser-based dev cockpit for Laravel',
    install: 'composer require fissible/watch',
    installLabel: 'composer',
    docsUrl: 'https://docs.fissible.dev/watch',
    githubUrl: 'https://github.com/fissible/watch',
    suite: 'php',
    features: [
      'Dashboard, route browser, drift detector, spec viewer, version manager',
      'Integrates accord, drift, forge, and fault in one UI',
      'MIT licensed — free to use in any Laravel project',
      'Filament 3 based — familiar admin UI conventions',
    ],
    codeExample: `// config/app.php
Fissible\\Watch\\WatchServiceProvider::class,

// Visit /watch in your browser`,
  },
  {
    slug: 'fault',
    name: 'fault',
    tagline: 'Exception tracking and triage for Laravel',
    install: 'composer require fissible/fault',
    installLabel: 'composer',
    docsUrl: 'https://docs.fissible.dev/fault',
    githubUrl: 'https://github.com/fissible/fault',
    suite: 'php',
    features: [
      'Fingerprinting and deduplication — one entry per unique exception',
      'Status workflow: open → investigating → resolved',
      'Test skeleton generation from captured exceptions',
      'Ships as part of the watch cockpit, MIT licensed',
    ],
    codeExample: `// Fault captures via the Laravel exception handler automatically.
// No manual instrumentation needed.
// View and triage at /watch/exceptions`,
  },
];

export const paidProducts: PaidProduct[] = [
  {
    slug: 'guit',
    name: 'guit',
    tagline: 'A terminal git client',
    description: 'A keyboard-driven terminal git client built on shellframe. Working copy, history, cherry-pick, branch graph, and a 3-pane merge resolver that registers as git mergetool.',
    status: 'coming-soon',
    formspreeId: 'xzdkzjyn',
  },
  {
    slug: 'sigil',
    name: 'sigil',
    tagline: 'Developer credential and connection broker',
    description: 'A local-first CLI that stores secrets, database connections, SSH profiles, and API tokens — with a pipeline-composable interface and OS keychain backend. Built in Rust.',
    status: 'purchase-pending',
    formspreeId: 'xeepornj',
  },
  {
    slug: 'station',
    name: 'Station',
    tagline: 'The Laravel CMS that enforces approval before anything goes live.',
    description: 'Self-hosted on your infrastructure. Filament v5 admin. Multi-tenant. One-time per-site license.',
    status: 'coming-soon',
    formspreeId: 'mojpvkrq',
    pricingNote: 'No subscriptions. No SaaS. Runs on your server.',
    screenshotsLabel: 'Approval queue · Content editor · Site switcher',
    features: [
      { title: 'Approval is not optional', body: 'Draft → Submit → Approve → Publish. Every piece of content must pass through an approver before it goes live. There is no "publish directly" shortcut.' },
      { title: 'Versioned by default', body: 'Every save creates a version. Roll back any content item to any previous state. Auditors can see exactly what changed, when, and who changed it.' },
      { title: 'Schedule without risk', body: 'Set a future publish date on approved content. Nothing can be scheduled that hasn\'t been approved. The scheduling system respects the workflow, not the other way around.' },
      { title: 'Multi-tenant from the start', body: 'Run multiple sites from a single Station install. Each site has its own content, its own users, and its own approval chain. No cross-site leakage.' },
      { title: 'Self-hosted, your data', body: 'Station runs on your server, in your infrastructure. No third-party SaaS in the content path. Your content, your database, your control.' },
      { title: 'Built on Filament v5', body: 'Laravel developers already know the admin conventions. Station extends Filament — familiar panels, familiar form components, extensible with standard Filament plugins.' },
    ],
    marketingSections: [
      {
        title: 'The problem with every other CMS',
        body: 'WordPress, Statamic, and most headless CMSs treat publishing as a permission problem: add a role, restrict the button. But roles are misconfigured. Editors get promoted. People click publish when they meant to save a draft. Station treats publishing as a workflow problem: the only way content reaches production is if an approver signed off on it. There is no other path.',
      },
      {
        title: 'Who uses this',
        body: 'Financial advisors and wealth management firms where compliance reviews every client communication. Law firms where content must be reviewed before publication. Healthcare providers under HIPAA marketing constraints. Any editorial team where "I thought it was approved" is not an acceptable post-mortem.',
      },
      {
        title: 'What v1 ships with',
        body: 'RichEditor content types. Draft → Submit → Approve → Publish workflow enforced at the model layer. Content versioning with rollback. Scheduled publishing (post-approval only). Multi-tenant architecture with isolated site contexts. Audit log. Filament v5 admin panel. Browser-based installer.',
      },
      {
        title: 'One-time license, no subscriptions',
        body: 'Station is not SaaS. You buy a per-site license, deploy it to your own server, and own it. No monthly fees. No data leaving your infrastructure. No vendor lock-in on your content pipeline.',
      },
    ],
  },
  {
    slug: 'conduit',
    name: 'conduit',
    tagline: 'A terminal HTTP client',
    description: 'Request builder, collections, response viewer, and sigil credential integration in the free tier. Paid tier adds accord/drift contract validation in-terminal.',
    status: 'coming-soon',
    formspreeId: 'xpqowybq',
  },
];

export const allOssPackages: OssPackage[] = [...tuiPackages, ...phpPackages];
