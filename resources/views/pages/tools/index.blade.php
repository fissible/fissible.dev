<x-layouts.marketing title="Tools" description="Open-source developer tools from fissible — API validation, TUI frameworks, and CLI utilities.">
<main class="tools-index">
    <header class="tools-header">
        <h1>Tools</h1>
        <p>Open-source tools that stand on their own and also serve as proof for the workflows and infrastructure ideas behind Station.</p>
    </header>

    <section class="tools-group">
        <h2>API Tools</h2>
        <div class="tools-list">
            @foreach($phpPackages as $p)
                <div class="tool-entry">
                    <div class="tool-entry-info">
                        <a href="/tools/{{ $p['slug'] }}" class="tool-entry-name">{{ $p['name'] }}</a>
                        <span class="tool-entry-tagline">{{ $p['tagline'] }}</span>
                    </div>
                    <a href="{{ $p['github_url'] }}" target="_blank" rel="noopener noreferrer" class="tool-entry-github">GitHub &#x2197;</a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="tools-group">
        <h2>CLI / TUI Tools</h2>
        <div class="tools-list">
            @foreach($tuiPackages as $p)
                <div class="tool-entry">
                    <div class="tool-entry-info">
                        <a href="/tools/{{ $p['slug'] }}" class="tool-entry-name">{{ $p['name'] }}</a>
                        <span class="tool-entry-tagline">{{ $p['tagline'] }}</span>
                    </div>
                    <a href="{{ $p['github_url'] }}" target="_blank" rel="noopener noreferrer" class="tool-entry-github">GitHub &#x2197;</a>
                </div>
            @endforeach
        </div>
    </section>
</main>
</x-layouts.marketing>
