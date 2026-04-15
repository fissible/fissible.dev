<x-layouts.marketing :title="$product['name']" :description="$product['tagline']">
<main class="coming-soon-page">
    <header class="coming-soon-hero">
        <p class="coming-soon-label">Coming Soon</p>
        <h1>{{ $product['name'] }}</h1>
        <p class="coming-soon-tagline">{{ $product['tagline'] }}</p>
        <p class="coming-soon-description">{{ $product['description'] }}</p>
        @if($product['slug'] === 'sigil')
            <p class="coming-soon-status">sigil v1 is complete. Available to purchase the moment our store opens.</p>
        @endif
    </header>
    <section class="waitlist-section">
        <h2>Get notified at launch</h2>
        <form class="waitlist-form" action="https://formspree.io/f/{{ $product['formspree_id'] }}" method="POST">
            <input type="email" name="email" placeholder="your@email.com" required>
            <input type="hidden" name="product" value="{{ $product['slug'] }}">
            <button type="submit">Notify me</button>
        </form>
    </section>
</main>
</x-layouts.marketing>
