<x-layouts.marketing title="Case Studies" description="Real products Fissible has designed, built, and shipped on its own stack — starting with Mesabit.">
<main class="cs-page">

    <header class="cs-hero">
        <p class="cs-kicker">Case Studies</p>
        <h1 class="cs-headline">Real products, built and run on the fissible stack.</h1>
        <p class="cs-subhead">We don&rsquo;t just ship developer tools &mdash; we use them to build and operate production software. Here&rsquo;s the work.</p>
    </header>

    <section class="cs-index" aria-label="Case study index">
        <a class="cs-card" href="#mesabit">
            <div class="cs-card-head">
                <span class="cs-card-name">Mesabit</span>
                <span class="cs-card-sector">Temporary internet &amp; Wi-Fi</span>
            </div>
            <p class="cs-card-summary">A complete lead-to-signed-proposal sales platform for on-demand connectivity in the Antelope Valley &mdash; designed, built, and operated by Fissible.</p>
            <div class="cs-card-tags">
                <span class="badge">Laravel 13</span>
                <span class="badge">Proposal engine</span>
                <span class="badge">Live in production</span>
            </div>
            <span class="cs-card-more">Read case study &rarr;</span>
        </a>
    </section>

    <section class="cs-detail" id="mesabit">
        <div class="cs-detail-head">
            <p class="cs-kicker">Case study</p>
            <h2 class="cs-detail-title">Mesabit</h2>
            <p class="cs-detail-tagline">Temporary internet and Wi-Fi for construction sites, events, and pop-ups across the Antelope Valley.</p>
            <div class="cs-meta-grid">
                <div>
                    <span class="cs-meta-label">Relationship</span>
                    <span class="cs-meta-value">A DBA designed, built &amp; operated by Fissible LLC</span>
                </div>
                <div>
                    <span class="cs-meta-label">Scope</span>
                    <span class="cs-meta-value">Full platform &mdash; marketing site, intake, proposal engine, ops console</span>
                </div>
                <div>
                    <span class="cs-meta-label">Status</span>
                    <span class="cs-meta-value">Live at <a href="https://mesabit.net" target="_blank" rel="noopener noreferrer">mesabit.net</a></span>
                </div>
            </div>
        </div>

        <div class="cs-block">
            <h3>The challenge</h3>
            <p>Mesabit sells three distinct connectivity products &mdash; construction-site internet, event Wi-Fi, and pop-up Wi-Fi &mdash; each with its own audience, pricing, and logistics. The business needed to turn scattered inbound leads into structured quotes and signed proposals quickly, without a sales team drowning in manual work, and without spam swamping the pipeline.</p>
        </div>

        <div class="cs-block">
            <h3>What we built</h3>
            <ul class="cs-list">
                <li><strong>Multi-landing marketing site</strong> &mdash; server-side hero rotation across construction, event, and pop-up variants, with keyword domains 301-redirecting in and UTM attribution captured through the funnel.</li>
                <li><strong>Unified quote intake</strong> &mdash; a single <code>/quote</code> form that normalizes wildly different site requirements into structured, deduplicated quote requests.</li>
                <li><strong>Deterministic recommendation engine</strong> &mdash; rule-versioned services translate intake data into package, pricing, line-item, and installer-checklist snapshots for a draft proposal.</li>
                <li><strong>Staff ops console</strong> &mdash; role-based <code>/ops</code> screens for proposal review, editing, publishing, revisions, resend, voiding, and installer checklists.</li>
                <li><strong>Tokenized proposals</strong> &mdash; public proposal pages with PDF downloads, online acceptance, and external payment-link handling.</li>
                <li><strong>Hardened intake</strong> &mdash; Cloudflare Turnstile, honeypot, minimum fill time, per-IP and per-email rate limits, and lead-time validation that rejects impossible timelines with a helpful next-best option.</li>
                <li><strong>Reliable notifications</strong> &mdash; Postmark-backed operator and customer emails with audit events and staff-visible delivery-failure state.</li>
            </ul>
        </div>

        <div class="cs-block">
            <h3>The stack</h3>
            <div class="cs-card-tags">
                <span class="badge">Laravel 13</span>
                <span class="badge">PHP 8.4</span>
                <span class="badge">SQLite (WAL)</span>
                <span class="badge">Blade</span>
                <span class="badge">Tailwind v4</span>
                <span class="badge">Pest</span>
                <span class="badge">Pint + Larastan</span>
                <span class="badge">Laravel Forge</span>
                <span class="badge">Postmark</span>
                <span class="badge">Cloudflare Turnstile</span>
            </div>
        </div>

        <div class="cs-block">
            <h3>The result</h3>
            <p>A complete lead-to-signed-proposal workflow running in production &mdash; deterministic quoting, spam-resistant intake, PDF proposals, and an operator console that keeps a lean operation moving. Built and shipped on the same stack and tooling fissible offers everyone else.</p>
        </div>

        <div class="cs-cta">
            <a href="https://mesabit.net" target="_blank" rel="noopener noreferrer" class="btn-primary">Visit mesabit.net &#x2197;</a>
        </div>
    </section>

</main>
</x-layouts.marketing>
