<x-layouts.marketing title="Station" description="Self-hosted Laravel CMS with enforced approval workflows. Multi-tenant, versioned, scheduled publishing. One-time per-site license.">
<main class="station-page">

    <!-- Hero -->
    <header class="station-hero">
        <p class="station-kicker">Station</p>
        <h1 class="station-headline">The self-hosted CMS for teams that cannot publish without approval.</h1>
        <p class="station-subhead">Built for regulated marketing, legal review, editorial teams, and multi-site operations that need enforced workflow, clean audit history, and infrastructure they control.</p>
        <div class="station-badges">
            <span class="badge">Self-Hosted</span>
            <span class="badge">Laravel + Filament v5</span>
            <span class="badge">One-time license</span>
        </div>
        <ul class="station-proof-points">
            <li>Approval is enforced in the workflow, not left to editor discipline</li>
            <li>Scheduled publishing respects review status before anything can go live</li>
            <li>Each site keeps its own users, content, and approval boundaries</li>
        </ul>
        <div class="station-cta">
            <a href="#waitlist" class="btn-primary">Join the Waitlist</a>
            @if($station['docs_url'])
                <a href="{{ $station['docs_url'] }}" class="btn-secondary">View Docs</a>
            @endif
            <a href="#how-it-works" class="btn-secondary">See how it works &darr;</a>
        </div>
        <div class="station-inline-form-wrap">
            <form class="waitlist-form station-inline-form" action="https://formspree.io/f/{{ $station['formspree_id'] }}" method="POST">
                <input type="email" name="email" placeholder="Work email" required>
                <input type="hidden" name="product" value="{{ $station['slug'] }}">
                <button type="submit">Request Early Access</button>
            </form>
            @if($station['pricing_note'])
                <p class="pricing-note">{{ $station['pricing_note'] }}</p>
            @endif
        </div>
    </header>

    <section class="station-decision">
        <div class="decision-card">
            <h2>What you are buying</h2>
            <p>A CMS where the publishing path is designed for approval-first teams: draft forks, required review, scheduled release after approval, version history, auditability, and tenant isolation.</p>
        </div>
        <div class="decision-card">
            <h2>Why it exists</h2>
            <p>Most CMS products treat "publish" as a permission toggle. Station treats it as workflow infrastructure, so a bad role change or rushed click does not send content live.</p>
        </div>
        <div class="decision-card">
            <h2>Who it fits</h2>
            <p>Teams with compliance, legal, franchise, or editorial review requirements that want to self-host instead of relying on SaaS publishing rules.</p>
        </div>
    </section>

    <section class="station-comparison">
        <div class="section-intro">
            <p class="section-kicker">Why teams switch</p>
            <h2>The economics and risk profile are different from per-site CMS setups.</h2>
        </div>
        <div class="comparison-grid">
            <div class="comparison-card">
                <h3>Per-site CMS licensing gets expensive fast</h3>
                <p>Agencies and multi-brand teams often pay once per site. Five customer sites can mean five separate CMS licenses. Station is designed around multi-tenancy, so one install can serve multiple tenants without a per-site license multiplier.</p>
            </div>
            <div class="comparison-card">
                <h3>Custom approval workflows are expensive to maintain</h3>
                <p>Building review, scheduling guards, role boundaries, and audit history yourself sounds straightforward until exceptions pile up. Station is meant to replace that internal workflow glue with one product path.</p>
            </div>
            <div class="comparison-card">
                <h3>SaaS admin rules are not the same as owning the publishing path</h3>
                <p>Station is self-hosted, so infrastructure, content, and operational behavior stay under your control. That matters when approval rules are part of your risk surface.</p>
            </div>
        </div>
    </section>

    <section class="station-diagram">
        <div class="section-intro">
            <p class="section-kicker">Implementation Diagram</p>
            <h2>A short visual for the operational model.</h2>
        </div>
        <figure class="diagram-frame">
            <img src="/assets-station/implementation-diagram.svg" alt="Station implementation diagram showing multiple tenant sites flowing through one self-hosted install with approval workflow, audit log, and isolated publishing boundaries." loading="lazy">
        </figure>
    </section>

    <!-- Workflow Steps -->
    <section class="station-workflow" id="how-it-works">
        <p class="workflow-label">Nothing goes live without approval</p>
        <div class="workflow-steps">
            @foreach(['Draft', 'Submit', 'Approve', 'Publish', 'Audit Log'] as $i => $step)
                <div class="workflow-item">
                    <span class="workflow-step{{ $step === 'Approve' ? ' workflow-step--accent' : '' }}">{{ $step }}</span>
                    @if($i < 4)
                        <span class="workflow-arrow">&rarr;</span>
                    @endif
                </div>
            @endforeach
        </div>
        <p class="workflow-note">Enforced at the model layer, not just a UI permission.</p>
    </section>

    <!-- Feature Grid -->
    @if(!empty($station['features']))
        <section class="station-features">
            @foreach($station['features'] as $feature)
                <div class="feature-block">
                    <h3 class="feature-title">{{ $feature['title'] }}</h3>
                    <p class="feature-body">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </section>
    @endif

    <!-- Deep-Dive Sections -->
    @if(!empty($station['marketing_sections']))
        <div class="station-sections">
            @foreach($station['marketing_sections'] as $section)
                <section class="station-section">
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                </section>
            @endforeach
        </div>
    @endif

    <!-- Use-Case Callout -->
    <section class="station-usecase">
        <div class="usecase-inner">
            <h2 class="usecase-heading">Built for teams where approval is mandatory, not optional</h2>
            <ul class="usecase-list">
                <li>Compliance officer must approve before any content reaches clients</li>
                <li>Nothing can be scheduled without prior approval</li>
                <li>Every version is retained — who changed what, and when</li>
                <li>Runs on your own server, under your own infrastructure</li>
            </ul>
        </div>
    </section>

    <section class="station-objections">
        <div class="section-intro">
            <p class="section-kicker">Objections</p>
            <h2>The decision is usually about risk, maintenance, or cost.</h2>
        </div>
        <div class="objection-grid">
            <div class="objection-card">
                <h3>Why not just add a publish approval step to our current CMS?</h3>
                <p>Because most systems still model publishing as a permission. Station models it as workflow state, so review requirements survive role drift, rushed clicks, and scheduling mistakes.</p>
            </div>
            <div class="objection-card">
                <h3>Why not build this in-house?</h3>
                <p>You can, but then your team owns every role edge case, scheduling guard, audit requirement, and tenant boundary forever. Station is for teams that want those constraints productized instead of custom-maintained.</p>
            </div>
            <div class="objection-card">
                <h3>Why is multi-tenancy important?</h3>
                <p>It changes the economics for agencies, franchise groups, and multi-brand teams. You can isolate each tenant while avoiding the operational sprawl and license multiplication of one CMS install per site.</p>
            </div>
        </div>
    </section>

    <!-- Waitlist Form -->
    <section class="station-waitlist" id="waitlist">
        <h2 class="waitlist-heading">Request early access.</h2>
        <p class="waitlist-sub">If you are evaluating approval workflow, multi-site CMS costs, or a replacement for custom publishing rules, join the list and I'll reach out when the first release is ready.</p>
        <form class="waitlist-form" action="https://formspree.io/f/{{ $station['formspree_id'] }}" method="POST">
            <input type="email" name="email" placeholder="Work email" required>
            <input type="hidden" name="product" value="{{ $station['slug'] }}">
            <button type="submit">Request Access</button>
        </form>
        <p class="waitlist-sub">Include your work email if you want product updates tied to a real team or use case.</p>
    </section>

</main>
</x-layouts.marketing>
