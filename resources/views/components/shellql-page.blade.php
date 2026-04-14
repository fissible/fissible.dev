@props(['package'])

<main class="shellql-page">

    <!-- Hero -->
    <header class="shellql-hero">
        <div class="hero-headline-row">
            <h1 class="hero-headline">SQLite workbench that runs over SSH</h1>
        </div>
        <p class="hero-subhead">Browse tables, run queries, and edit rows directly on the server. No GUI. No port forwarding.</p>
    </header>

    <!-- Quick Install -->
    <section class="shellql-install">
        <pre class="install-code"><code>{{ $package['install'] }}
shql my.db</code></pre>
        <p class="install-meta">Runs on bash 3.2&ndash;5.2 (macOS + Linux)</p>
        <p class="install-meta">No dependencies beyond sqlite3</p>
        <div class="install-cta">
            <a href="{{ $package['github_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-secondary">GitHub &#x2197;</a>
            <a href="{{ $package['docs_url'] }}" class="btn-secondary">View Docs</a>
        </div>
    </section>

    <!-- Proof Strip -->
    <section class="shellql-proof">
        <div class="proof-grid">
            @foreach([
                ['src' => '/screenshots/shellql/fission1.png', 'caption' => 'Jump between databases instantly'],
                ['src' => '/screenshots/shellql/fission4.png', 'caption' => 'Filter and scan large tables fast'],
                ['src' => '/screenshots/shellql/fission5.png', 'caption' => 'Edit rows with schema-aware fields'],
            ] as $shot)
                <div class="proof-item">
                    <img src="{{ $shot['src'] }}" alt="{{ $shot['caption'] }}" class="proof-img" loading="lazy">
                    <p class="proof-caption">{{ $shot['caption'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Terminal Preview -->
    <section class="shellql-preview">
        <img src="/shellql/demo.gif" alt="ShellQL demo — browse tables, filter rows, inspect records" class="demo-gif">
    </section>

    <!-- Why This Exists -->
    <section class="shellql-why">
        <p>You SSH into a server. The SQLite database is right there &mdash; and every GUI tool you own stops working.</p>
        <p>ShellQL lets you browse and edit it directly, without leaving the terminal.</p>
    </section>

    <!-- Feature Blocks -->
    <section class="shellql-features">
        @foreach([
            ['title' => 'Do real work, not just read', 'body' => 'Insert, update, delete. Most terminal tools stop at read-only.'],
            ['title' => 'Built for remote environments', 'body' => 'Run over SSH with zero setup. No tunnels, no file syncing.'],
            ['title' => 'Multi-tab workflow', 'body' => 'Open tables and queries side by side. Switch instantly with tabs and shortcuts.'],
            ['title' => 'Mouse or keyboard', 'body' => 'Use it like vim or like a GUI. Both paths work.'],
        ] as $feature)
            <div class="feature-block">
                <h3 class="feature-title">{{ $feature['title'] }}</h3>
                <p class="feature-body">{{ $feature['body'] }}</p>
            </div>
        @endforeach
    </section>

    <!-- Deep-Dive Sections -->
    @if(!empty($package['marketing_sections']))
        <div class="shellql-sections">
            @foreach($package['marketing_sections'] as $section)
                <section class="shellql-section">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                    @if(!empty($section['code']))
                        <pre><code>{{ $section['code'] }}</code></pre>
                    @endif
                </section>
            @endforeach
        </div>
    @endif

    <!-- Bottom Install -->
    <section class="shellql-install shellql-install--bottom">
        <pre class="install-code"><code>{{ $package['install'] }}</code></pre>
        <div class="install-cta">
            <a href="{{ $package['github_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-secondary">GitHub &#x2197;</a>
            <a href="{{ $package['docs_url'] }}" class="btn-secondary">View Docs</a>
        </div>
    </section>

</main>
