@php
    // Early-access capture. TODO: replace with the Fissible Phone Formspree form ID.
    $formspreeId = 'YOUR_PHONE_FORM_ID';
@endphp
<x-layouts.marketing
    title="Fissible Phone · Business Phone App for iPhone"
    description="Fissible Phone is an iOS business phone app for multi-line calling, voicemail, caller screening, and future AI receptionist workflows. Built for small businesses that need a premium phone system on iPhone.">
<main class="phone-page">

    {{-- Hero --}}
    <header class="phone-hero">
        <div class="phone-hero-copy">
            <p class="phone-kicker">Fissible Phone &middot; In development</p>
            <h1 class="phone-headline">A Premium Business Phone App for iPhone</h1>
            <p class="phone-subhead">Fissible Phone gives small businesses multi-line calling, caller screening, voicemail intelligence, and the foundation for AI receptionist workflows &mdash; without the PBX complexity.</p>
            <ul class="phone-hero-bullets">
                <li>Keep business calls separate from your personal number</li>
                <li>Manage multiple business lines from one iPhone app</li>
                <li>Screen unknown callers before they interrupt your day</li>
                <li>Review voicemail with transcription and structured summaries</li>
                <li>English and Spanish (US) &mdash; built bilingual</li>
            </ul>
            <div class="phone-cta-row">
                <a href="#early-access" class="btn-primary">Request Early Access</a>
                <a href="#features" class="btn-secondary">See Features &darr;</a>
            </div>
            <p class="phone-trust">Built by Fissible. Powered by Twilio infrastructure. Designed for iPhone-first business communication.</p>
        </div>

        {{-- Screenshot-free product mock: caller-screening call screen --}}
        <div class="phone-mock" aria-hidden="true">
            <div class="phone-mock-frame">
                <div class="phone-mock-notch"></div>
                <div class="phone-mock-screen">
                    <p class="pm-line">Business line &middot; Sales</p>
                    <div class="pm-avatar">?</div>
                    <p class="pm-caller">Unknown Caller</p>
                    <p class="pm-status">Screening&hellip;</p>
                    <p class="pm-sub">(661) 555&ndash;0148 &middot; No caller ID</p>
                    <div class="pm-actions">
                        <span class="pm-btn pm-decline">Decline</span>
                        <span class="pm-btn pm-vm">Voicemail</span>
                        <span class="pm-btn pm-accept">Accept</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Problem --}}
    <section class="phone-section">
        <h2 class="phone-h2">Business phone tools shouldn&rsquo;t feel like telecom homework</h2>
        <div class="phone-prose">
            <p>Most small business phone setups force you into one of two bad options: forward everything to your personal cell, or stitch together phone numbers, SIP trunks, forwarding rules, softphones, and dashboards.</p>
            <p>That works until it doesn&rsquo;t. You miss calls. Spam gets through. Voicemail is hard to review. Business and personal blur together. And when you grow from one person to a small team, the whole setup becomes fragile.</p>
            <p>Fissible Phone is being built to make the business phone experience feel native, organized, and professional on iPhone.</p>
        </div>
        <ul class="phone-list phone-list-cols">
            <li>Personal and business calls get mixed together</li>
            <li>Unknown callers interrupt focused work</li>
            <li>Voicemail is slow to review</li>
            <li>Multi-line setups are awkward</li>
            <li>Existing Twilio workflows require too much setup</li>
            <li>Traditional PBX systems feel heavy for small teams</li>
        </ul>
    </section>

    {{-- Solution / feature cards --}}
    <section class="phone-section" id="features">
        <h2 class="phone-h2">A business phone layer built around the iPhone</h2>
        <p class="phone-lead">Fissible Phone turns your business number into a dedicated iOS calling experience &mdash; the control of a real phone system without making every owner or employee a telecom admin.</p>
        <div class="phone-cards">
            <article class="phone-card">
                <h3>Multi-line calling</h3>
                <p>Manage multiple business numbers from one app. Choose which line you call from and keep conversations organized by business, brand, or department.</p>
            </article>
            <article class="phone-card">
                <h3>Caller screening</h3>
                <p>Unknown callers shouldn&rsquo;t get instant access to your attention. Fissible Phone is designed around screening, context, and smarter call handling.</p>
            </article>
            <article class="phone-card">
                <h3>Voicemail that&rsquo;s easier to review</h3>
                <p>Review voicemail with transcription, caller details, and structured analysis, so you can decide what needs action without listening end to end.</p>
            </article>
            <article class="phone-card">
                <h3>Built for multi-business use</h3>
                <p>Operate more than one business or brand from the same device while keeping calls and lines cleanly separated.</p>
            </article>
            <article class="phone-card">
                <h3>Bilingual by design</h3>
                <p>English and Spanish (US) across the app experience, so bilingual owners and teams can work in the language their customers speak.</p>
            </article>
            <article class="phone-card">
                <h3>iPhone-first experience</h3>
                <p>Designed around iOS calling patterns &mdash; not a desktop PBX interface squeezed onto a phone.</p>
            </article>
        </div>
    </section>

    {{-- Call flow --}}
    <section class="phone-section">
        <h2 class="phone-h2">Designed for the real call flow</h2>
        <p class="phone-lead">From unknown caller to follow-up, Fissible Phone is built around the full lifecycle of a business call.</p>
        <ol class="phone-flow">
            <li>
                <span class="phone-flow-num">1</span>
                <div>
                    <h3>A caller reaches your business line</h3>
                    <p>Calls come into your dedicated business number, separate from your personal phone identity.</p>
                </div>
            </li>
            <li>
                <span class="phone-flow-num">2</span>
                <div>
                    <h3>Screening gives you context</h3>
                    <p>Unknown callers can be screened before they reach you directly, helping reduce spam, scams, and low-value interruptions.</p>
                </div>
            </li>
            <li>
                <span class="phone-flow-num">3</span>
                <div>
                    <h3>You answer, decline, or route</h3>
                    <p>Take the call in the app, send it to voicemail, or let the system handle the next step based on your setup.</p>
                </div>
            </li>
            <li>
                <span class="phone-flow-num">4</span>
                <div>
                    <h3>Voicemail becomes actionable</h3>
                    <p>Missed calls and voicemail can be reviewed later with transcription and analysis, helping you prioritize urgent calls.</p>
                </div>
            </li>
            <li>
                <span class="phone-flow-num">5</span>
                <div>
                    <h3>Follow-up stays connected to the business</h3>
                    <p>Call back from the right business line instead of exposing your personal number.</p>
                </div>
            </li>
        </ol>
    </section>

    {{-- Screening emphasis --}}
    <section class="phone-section phone-feature-split">
        <div>
            <h2 class="phone-h2">Caller screening is not an add-on. It&rsquo;s the point.</h2>
            <div class="phone-prose">
                <p>Small businesses are constant targets for spam, robocalls, fake leads, vendor noise, and outright scams. A business phone system should protect your attention, not just ring louder.</p>
                <p>Fissible Phone is built with scam and spam screening at the center &mdash; better context before you answer, and better records when you don&rsquo;t.</p>
            </div>
        </div>
        <ul class="phone-list">
            <li>Screen unknown callers before they reach you directly</li>
            <li>Separate real customers from noise</li>
            <li>Reduce interruptions from spam and scam calls</li>
            <li>Preserve professional call handling even when you are busy</li>
            <li>Build a history of calls, voicemail, and caller context</li>
        </ul>
    </section>

    {{-- Voicemail --}}
    <section class="phone-section phone-feature-split">
        <div>
            <h2 class="phone-h2">Voicemail you can actually use</h2>
            <div class="phone-prose">
                <p>Traditional voicemail is a backlog. Fissible Phone is designed to make voicemail easier to triage.</p>
                <p>Instead of only a recording, voicemail can include transcription, caller details, urgency indicators, and structured notes &mdash; so you can decide what deserves a callback, what can wait, and what to ignore.</p>
            </div>
        </div>
        <ul class="phone-list">
            <li>Voicemail inbox built for business follow-up</li>
            <li>Transcription for faster review</li>
            <li>Caller and company extraction where available</li>
            <li>Urgency and intent indicators</li>
            <li>Block or add callers from voicemail context</li>
        </ul>
    </section>

    {{-- Multi-line / multi-business --}}
    <section class="phone-section">
        <h2 class="phone-h2">One app for multiple lines, brands, or businesses</h2>
        <p class="phone-lead">Many owners don&rsquo;t operate in a single neat box &mdash; a main line, a second brand, a side operation, or separate numbers for different teams. Fissible Phone is being built for that reality, so calls stay organized instead of collapsing into one generic log.</p>
        <ul class="phone-list phone-list-cols">
            <li>Owner with separate business and personal numbers</li>
            <li>Consultant managing multiple brands</li>
            <li>Small business with sales and support lines</li>
            <li>Founder testing a new business line before hiring</li>
            <li>Team with different numbers per employee</li>
            <li>Multi-brand operator keeping lines distinct</li>
        </ul>
    </section>

    {{-- AI receptionist --}}
    <section class="phone-section">
        <div class="phone-h2-row">
            <h2 class="phone-h2">Coming next: AI receptionist for small business</h2>
            <span class="badge phone-badge-planned">Planned &middot; paid subscription</span>
        </div>
        <p class="phone-lead">Fissible Phone is being built as the foundation for an AI receptionist subscription: helping small businesses answer more calls, screen unknown callers, capture details, summarize conversations, and route calls without a full-time front desk.</p>
        <ul class="phone-list phone-list-cols">
            <li>Answer and screen calls when you are unavailable</li>
            <li>Ask callers for name, company, and reason for calling</li>
            <li>Answer callers in English or Spanish (US)</li>
            <li>Summarize call intent and detect urgency</li>
            <li>Route callers based on business rules</li>
            <li>Capture callback details and reduce low-value interruptions</li>
        </ul>
    </section>

    {{-- Twilio positioning --}}
    <section class="phone-section">
        <h2 class="phone-h2">Twilio power without the Twilio setup</h2>
        <div class="phone-prose">
            <p>Twilio is powerful infrastructure, but turning a Twilio number into a polished iPhone business phone usually means configuring credentials, softphones, forwarding rules, Studio flows, SIP trunks, or custom code. Fissible Phone is designed to turn that infrastructure into a complete business phone experience.</p>
        </div>
        <ul class="phone-list phone-list-cols">
            <li>Built on proven communications infrastructure</li>
            <li>Designed for iPhone users, not telecom admins</li>
            <li>Avoids fragile DIY softphone setups</li>
            <li>Gives small businesses a more polished call workflow</li>
            <li>A path from simple line to receptionist automation</li>
        </ul>
        <p class="phone-legal">Fissible Phone uses Twilio-powered communication infrastructure. It is not affiliated with, sponsored by, or endorsed by Twilio.</p>
    </section>

    {{-- Comparison --}}
    <section class="phone-section">
        <h2 class="phone-h2">Not just another softphone</h2>
        <div class="phone-compare-wrap">
            <table class="phone-compare">
                <thead>
                    <tr><th>Typical softphone</th><th>Fissible Phone</th></tr>
                </thead>
                <tbody>
                    <tr><td>Connects to a number</td><td>Organizes calling around lines, contacts, voicemail, and screening</td></tr>
                    <tr><td>Requires telecom setup</td><td>Designed to reduce setup complexity</td></tr>
                    <tr><td>Treats every call the same</td><td>Focuses on caller context and screening</td></tr>
                    <tr><td>Generic call history</td><td>Business-focused recents, contacts, and voicemail</td></tr>
                    <tr><td>Usually one account, one line</td><td>Built toward multi-line and multi-business workflows</td></tr>
                    <tr><td>Mostly reactive</td><td>A foundation for AI receptionist automation</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- Audience --}}
    <section class="phone-section">
        <h2 class="phone-h2">Built for owner-led businesses and small teams</h2>
        <p class="phone-lead">Fissible Phone is for businesses too serious for a personal cell number, but not ready for a heavyweight enterprise phone system.</p>
        <div class="phone-audience">
            <span class="badge">Local service businesses</span>
            <span class="badge">Consultants</span>
            <span class="badge">Agencies</span>
            <span class="badge">Real estate</span>
            <span class="badge">Solo founders</span>
            <span class="badge">Small teams</span>
            <span class="badge">Multi-brand operators</span>
            <span class="badge">Twilio-backed numbers</span>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="phone-section">
        <h2 class="phone-h2">Frequently asked questions</h2>
        <div class="phone-faq">
            <details>
                <summary>Is Fissible Phone available now?</summary>
                <p>Fissible Phone is currently in development. Early testing is underway, and the product is being prepared for broader availability.</p>
            </details>
            <details>
                <summary>Is this a replacement for my personal phone number?</summary>
                <p>No. Fissible Phone keeps your business number separate from your personal number while letting you manage business calls from your iPhone.</p>
            </details>
            <details>
                <summary>Does it support multiple business lines?</summary>
                <p>Yes &mdash; multi-line and multi-business workflows are central to the product direction.</p>
            </details>
            <details>
                <summary>Does it work with Twilio?</summary>
                <p>Fissible Phone is built around Twilio-backed calling infrastructure, so you get a polished iPhone business phone experience without assembling your own softphone setup.</p>
            </details>
            <details>
                <summary>Is Fissible Phone affiliated with Twilio?</summary>
                <p>No. Fissible Phone is built by Fissible and is not affiliated with, sponsored by, or endorsed by Twilio.</p>
            </details>
            <details>
                <summary>Is it available in Spanish?</summary>
                <p>Yes. Fissible Phone is being built bilingual, with support for English and Spanish (US).</p>
            </details>
            <details>
                <summary>Will it support SMS?</summary>
                <p>SMS support is planned. The initial focus is business calling, caller screening, voicemail, and call workflow quality.</p>
            </details>
            <details>
                <summary>What is the AI receptionist?</summary>
                <p>The planned AI receptionist is a future paid subscription feature intended to help answer, screen, summarize, and route business calls.</p>
            </details>
            <details>
                <summary>Can it help with spam and scam calls?</summary>
                <p>Caller screening is a central product goal. Fissible Phone is designed to give businesses more context and control before unknown callers interrupt the workday.</p>
            </details>
            <details>
                <summary>Do I need to understand SIP, PBX, or Twilio Studio?</summary>
                <p>No. The product direction is to hide as much telecom complexity as possible behind a clean iOS business phone experience.</p>
            </details>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="phone-cta" id="early-access">
        <h2 class="phone-h2">Get early access to Fissible Phone</h2>
        <p class="phone-lead">Fissible Phone is in active development. Join the early access list to follow progress, test the app, and help shape the business phone system we&rsquo;re building for iPhone-first teams.</p>
        <form class="waitlist-form" action="https://formspree.io/f/{{ $formspreeId }}" method="POST">
            <input type="email" name="email" placeholder="Work email" required>
            <input type="hidden" name="product" value="phone">
            <button type="submit">Request Early Access</button>
        </form>
        <p class="phone-trust">Built by Fissible for small businesses that need a smarter, cleaner way to handle calls. Fissible Phone is not affiliated with or endorsed by Twilio.</p>
    </section>

</main>
</x-layouts.marketing>
