<x-layouts.marketing title="Station" description="Self-hosted Laravel CMS for agencies managing multiple client sites with enforced approval workflows, tenant isolation, and predictable hosting economics.">
<main class="station-page">

    <!-- Hero -->
    <header class="station-hero">
        <p class="station-kicker">Station</p>
        <h1 class="station-headline">The self-hosted CMS for agencies managing 5-20 client sites.</h1>
        <p class="station-subhead">Run client sites from one Laravel install, give each client an approval workflow, and stop adding a new CMS subscription or deployment every time you win an account.</p>
        <div class="station-badges">
            <span class="badge">Multi-tenant client sites</span>
            <span class="badge">Client approvals</span>
            <span class="badge">Self-hosted Laravel</span>
        </div>
        <ul class="station-proof-points">
            <li>Each client gets isolated users, content, menus, and approval boundaries</li>
            <li>Client sign-off is enforced before scheduled or manual publishing</li>
            <li>One install can serve many client sites without per-site CMS sprawl</li>
        </ul>
        <div class="station-cta">
            <a href="#waitlist" class="btn-primary">Join the Waitlist</a>
            @if($station['docs_url'])
                <a href="{{ $station['docs_url'] }}" class="btn-secondary">View Docs</a>
            @endif
            <a href="#how-it-works" class="btn-secondary">See how it works &darr;</a>
            <a href="/station/api-pro" class="btn-secondary">API Pro Module</a>
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
            <p>A multi-tenant CMS for agencies: client sites, client approvals, scheduling, version history, menus, and auditability in one self-hosted Laravel install.</p>
        </div>
        <div class="decision-card">
            <h2>Why it exists</h2>
            <p>Most agency CMS work becomes a pile of separate installs, plugins, client logins, and approval workarounds. Station turns that repeatable mess into one product path.</p>
        </div>
        <div class="decision-card">
            <h2>Who it fits</h2>
            <p>Small agencies managing roughly 5-20 client sites that need client sign-off before publishing and want predictable hosting instead of another CMS bill per account.</p>
        </div>
    </section>

    <section class="station-comparison">
        <div class="section-intro">
            <p class="section-kicker">Why agencies switch</p>
            <h2>The economics and risk profile are different when every client site becomes another CMS.</h2>
        </div>
        <div class="comparison-grid">
            <div class="comparison-card">
                <h3>Per-site CMS licensing gets expensive fast</h3>
                <p>Five client sites can mean five CMS subscriptions, five update surfaces, and five sets of approval rules. Station is designed around multi-tenancy, so one install can serve multiple client tenants without multiplying the stack.</p>
            </div>
            <div class="comparison-card">
                <h3>Client approval should not live in email</h3>
                <p>Agencies often stitch together email, docs, CMS roles, and Slack messages to prove a client approved something. Station makes approval part of the publishing path.</p>
            </div>
            <div class="comparison-card">
                <h3>Self-hosting keeps the client stack yours</h3>
                <p>Station runs on your infrastructure, so client content, hosting decisions, and operational behavior stay under your control instead of being spread across another SaaS dashboard.</p>
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
        <p class="workflow-label">Client content does not go live without approval</p>
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
        <p class="workflow-note">Client sign-off is workflow state, not just a role or a note in an email thread.</p>
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
            <h2 class="usecase-heading">Built for agencies where client approval is mandatory, not optional</h2>
            <ul class="usecase-list">
                <li>Client stakeholders can approve content before it reaches production</li>
                <li>Nothing can be scheduled without the right approval state</li>
                <li>Every version is retained: who changed what, who approved it, and when</li>
                <li>Each client stays isolated while your agency runs one operational stack</li>
            </ul>
        </div>
    </section>

    <section class="station-comparison">
        <div class="section-intro">
            <p class="section-kicker">Station Modules</p>
            <h2>API Pro extends Station beyond content into client integration operations.</h2>
        </div>
        <div class="comparison-grid">
            <div class="comparison-card">
                <h3>API Pro</h3>
                <p>Contract-aware API management for agencies that maintain Laravel client sites with third-party integrations, OpenAPI specs, and change risk.</p>
                <p><a href="/station/api-pro" class="tool-entry-github">Read the API Pro preview &rarr;</a></p>
            </div>
            <div class="comparison-card">
                <h3>Built on inspectable tools</h3>
                <p>API Pro packages the workflow behind public fissible tools like Accord, Drift, and Forge so the technical direction is visible before the module ships.</p>
            </div>
            <div class="comparison-card">
                <h3>Same agency operating model</h3>
                <p>Client sites, content approvals, tenant isolation, and API change visibility should live in one operational surface instead of separate dashboards.</p>
            </div>
        </div>
    </section>

    <section class="station-objections">
        <div class="section-intro">
            <p class="section-kicker">Objections</p>
            <h2>The decision is usually about risk, maintenance, or cost.</h2>
        </div>
        <div class="objection-grid">
            <div class="objection-card">
                <h3>Why not just use WordPress roles and a plugin?</h3>
                <p>You can, but every client site becomes its own combination of plugins, permissions, and exceptions. Station puts client approval and tenant isolation into the core product path.</p>
            </div>
            <div class="objection-card">
                <h3>Why not build this in-house?</h3>
                <p>You can, but then your agency owns every role edge case, scheduling guard, audit requirement, and client boundary forever. Station is for teams that want those constraints productized instead of custom-maintained.</p>
            </div>
            <div class="objection-card">
                <h3>Why is multi-tenancy important?</h3>
                <p>It changes the economics. You can isolate each client while avoiding the operational sprawl and license multiplication of one CMS install per account.</p>
            </div>
        </div>
    </section>

    <!-- Waitlist Form -->
    <section class="station-waitlist" id="waitlist">
        <h2 class="waitlist-heading">Request early access.</h2>
        <p class="waitlist-sub">If your agency manages multiple client sites and wants fewer CMS installs, safer approvals, or a self-hosted Laravel path, join the list and I'll reach out when the first release is ready.</p>
        <form class="waitlist-form" action="https://formspree.io/f/{{ $station['formspree_id'] }}" method="POST">
            <input type="email" name="email" placeholder="Work email" required>
            <input type="hidden" name="product" value="{{ $station['slug'] }}">
            <button type="submit">Request Access</button>
        </form>
        <p class="waitlist-sub">Include your work email and the rough number of client sites you manage if you want a useful follow-up.</p>
    </section>

</main>
</x-layouts.marketing>
