// apps/site/src/data/packages.ts

export interface OssPackage {
  slug: string;
  name: string;
  tagline: string;
  install: string;
  installLabel: 'brew' | 'composer';
  docsUrl: string;
  githubUrl: string;
  features: [string, string, string, string];
  codeExample: string;
  suite: 'tui' | 'php';
}

export interface PaidProduct {
  slug: string;
  name: string;
  tagline: string;
  description: string;
  status: 'coming-soon' | 'purchase-pending';
  formspreeId: string;
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
  },
  {
    slug: 'shellql',
    name: 'shellql',
    tagline: 'Terminal SQLite workbench',
    install: 'brew install fissible/tap/shellql',
    installLabel: 'brew',
    docsUrl: 'https://docs.fissible.dev/shellql',
    githubUrl: 'https://github.com/fissible/shellql',
    suite: 'tui',
    features: [
      'Schema browser, table view, query screen, and record inspector',
      'Real sqlite3 adapter — list tables, describe schema, fetch and filter rows',
      'Built on shellframe — keyboard-driven, no mouse required',
      'Free and MIT-licensed; enterprise edition planned',
    ],
    codeExample: `# Open a database
shql myapp.db

# Use a named connection from sigil
shql --connection production`,
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
    name: 'station',
    tagline: 'A self-hosted Laravel CMS platform',
    description: 'Schema-driven content types, a Draft→Review→Published approval workflow, and a browser-based installer. One-time per-site license.',
    status: 'coming-soon',
    formspreeId: 'mojpvkrq',
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
