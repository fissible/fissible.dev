@php
    $station = collect(config('packages.paid'))->firstWhere('slug', 'station');
@endphp

<x-layouts.marketing title="API Pro" description="API Pro is a Station module for agencies that need contract-aware APIs, client-safe integrations, and operational visibility across Laravel-powered client sites.">
<main class="station-page">

    <header class="station-hero">
        <p class="station-kicker">Station Module</p>
        <h1 class="station-headline">API Pro keeps client integrations from becoming invisible maintenance debt.</h1>
        <p class="station-subhead">API Pro adds contract-aware API operations to Station, so agencies can manage client integrations with the same discipline they bring to content approvals, tenant boundaries, and publishing workflows.</p>
        <div class="station-badges">
            <span class="badge">Coming soon</span>
            <span class="badge">Built for Laravel agencies</span>
            <span class="badge">Powered by fissible API tools</span>
        </div>
        <ul class="station-proof-points">
            <li>Track API contracts per client site instead of relying on scattered specs and tribal knowledge</li>
            <li>Catch drift between Laravel routes, OpenAPI specs, and real application behavior before clients do</li>
            <li>Keep client-facing integrations inside the same Station operating model as content, users, and tenants</li>
        </ul>
        <div class="station-cta">
            <a href="#waitlist" class="btn-primary">Ask About API Pro</a>
            <a href="/station" class="btn-secondary">Back to Station</a>
            <a href="https://docs.fissible.dev/accord/" class="btn-secondary">View API Tools</a>
        </div>
    </header>

    <section class="station-decision">
        <div class="decision-card">
            <h2>What it is</h2>
            <p>A Station module for contract-aware API management: route discovery, OpenAPI scaffolding, validation, drift checks, and change visibility for client sites.</p>
        </div>
        <div class="decision-card">
            <h2>Who it is for</h2>
            <p>Agencies with client sites that expose APIs, integrate with third parties, or need a safer way to prove that backend changes did not quietly break consumers.</p>
        </div>
        <div class="decision-card">
            <h2>Why it belongs in Station</h2>
            <p>Station already knows the tenant, the client, the deployment context, and the people responsible. API Pro brings integration risk into that same control surface.</p>
        </div>
    </section>

    <section class="station-comparison">
        <div class="section-intro">
            <p class="section-kicker">Agency API Work</p>
            <h2>Most API problems are not framework problems. They are ownership, visibility, and change-control problems.</h2>
        </div>
        <div class="comparison-grid">
            <div class="comparison-card">
                <h3>Specs drift away from code</h3>
                <p>OpenAPI files are useful only if they stay connected to routes, request validation, responses, and releases. API Pro is designed to make that relationship visible inside Station.</p>
            </div>
            <div class="comparison-card">
                <h3>Client integrations need auditability</h3>
                <p>When a client asks why an integration changed, your team should be able to see what changed, when it changed, and whether the change was expected.</p>
            </div>
            <div class="comparison-card">
                <h3>One-off API work does not scale</h3>
                <p>Each custom integration adds another place where an agency can accidentally own undocumented behavior forever. API Pro turns repeatable checks into product behavior.</p>
            </div>
        </div>
    </section>

    <section class="station-workflow" id="how-it-works">
        <p class="workflow-label">Intended API Pro workflow</p>
        <div class="workflow-steps">
            @foreach(['Discover', 'Document', 'Validate', 'Detect Drift', 'Review Change'] as $i => $step)
                <div class="workflow-item">
                    <span class="workflow-step{{ $step === 'Detect Drift' ? ' workflow-step--accent' : '' }}">{{ $step }}</span>
                    @if($i < 4)
                        <span class="workflow-arrow">&rarr;</span>
                    @endif
                </div>
            @endforeach
        </div>
        <p class="workflow-note">The goal is not another docs page. The goal is to make API change risk visible before it becomes client support work.</p>
    </section>

    <section class="station-features">
        <div class="feature-block">
            <h3 class="feature-title">Route-to-contract scaffolding</h3>
            <p class="feature-body">Use Laravel routes and validation patterns as the starting point for OpenAPI specs, then keep the generated contract close to the app that owns it.</p>
        </div>
        <div class="feature-block">
            <h3 class="feature-title">Runtime contract validation</h3>
            <p class="feature-body">Validate requests and responses against the expected API contract so integration breakage is caught closer to the release that introduced it.</p>
        </div>
        <div class="feature-block">
            <h3 class="feature-title">Drift and change detection</h3>
            <p class="feature-body">Compare contracts over time, identify breaking changes, and give teams a clearer path from code change to client-safe release notes.</p>
        </div>
        <div class="feature-block">
            <h3 class="feature-title">Tenant-aware visibility</h3>
            <p class="feature-body">Attach API status and change history to the Station tenant model so each client site has its own integration context.</p>
        </div>
    </section>

    <section class="station-usecase">
        <div class="usecase-inner">
            <h2 class="usecase-heading">API Pro is worth showing before it is fully launched if the promise stays narrow.</h2>
            <ul class="usecase-list">
                <li>Position it as a Station module in progress, not a standalone finished product</li>
                <li>Use it to qualify agencies that have real API maintenance pain</li>
                <li>Keep the CTA conversational until install, pricing, and docs are finalized</li>
                <li>Let the public Accord, Drift, and Forge tools prove the technical direction</li>
            </ul>
        </div>
    </section>

    <section class="station-objections">
        <div class="section-intro">
            <p class="section-kicker">Status</p>
            <h2>It is not too soon, but the page should not overpromise availability.</h2>
        </div>
        <div class="objection-grid">
            <div class="objection-card">
                <h3>Is API Pro generally available?</h3>
                <p>Not yet. This page should be treated as an early module preview for qualified Station conversations.</p>
            </div>
            <div class="objection-card">
                <h3>Does it replace the public API tools?</h3>
                <p>No. Accord, Drift, and Forge remain inspectable building blocks. API Pro packages the workflow into Station.</p>
            </div>
            <div class="objection-card">
                <h3>Why mention it now?</h3>
                <p>For agencies, APIs are part of the client-site operating model. Mentioning API Pro helps Station feel like a platform, not just a content editor.</p>
            </div>
        </div>
    </section>

    <section class="station-waitlist" id="waitlist">
        <h2 class="waitlist-heading">Ask about API Pro.</h2>
        <p class="waitlist-sub">If your agency manages Laravel client sites with API integrations, ask about API Pro and include the rough number of sites or integrations you maintain.</p>
        <form class="waitlist-form" action="https://formspree.io/f/{{ $station['formspree_id'] ?? 'mojpvkrq' }}" method="POST">
            <input type="email" name="email" placeholder="Work email" required>
            <input type="hidden" name="product" value="api-pro">
            <input type="hidden" name="context" value="station-module">
            <button type="submit">Ask About API Pro</button>
        </form>
    </section>

</main>
</x-layouts.marketing>
