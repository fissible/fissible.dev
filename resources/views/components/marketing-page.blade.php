@props(['package'])

<main class="marketing-page">
    <header class="marketing-hero">
        <h1>{{ $package['name'] }}</h1>
        <p class="marketing-tagline">{{ $package['tagline'] }}</p>
        <div class="install-block">
            <code>{{ $package['install'] }}</code>
        </div>
        @if($package['install_note'])
            <p class="install-note">{{ $package['install_note'] }}</p>
        @endif
        <div class="cta-row">
            <a href="{{ $package['docs_url'] }}" class="btn-primary">View Docs</a>
            <a href="{{ $package['github_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-secondary">GitHub &#x2197;</a>
        </div>
    </header>

    @if($package['description'])
        <p class="marketing-description">{{ $package['description'] }}</p>
    @endif

    @if(!empty($package['screenshots']))
        <section class="marketing-screenshots">
            @if($package['screenshots_label'])
                <p class="screenshots-label">{{ $package['screenshots_label'] }}</p>
            @endif
            <div class="screenshots-grid">
                @foreach($package['screenshots'] as $src)
                    <img src="{{ $src }}" alt="" class="screenshot" loading="lazy">
                @endforeach
            </div>
        </section>
    @endif

    <section class="marketing-features">
        <h2>Features</h2>
        <ul>
            @foreach($package['features'] as $f)
                <li>{{ $f }}</li>
            @endforeach
        </ul>
    </section>

    <section class="marketing-example">
        <h2>Example</h2>
        <pre><code>{{ $package['code_example'] }}</code></pre>
    </section>

    @if(!empty($package['marketing_sections']))
        <div class="marketing-sections">
            @foreach($package['marketing_sections'] as $section)
                <section class="marketing-section">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                    @if(!empty($section['code']))
                        <pre><code>{{ $section['code'] }}</code></pre>
                    @endif
                </section>
            @endforeach
        </div>
    @endif
</main>
