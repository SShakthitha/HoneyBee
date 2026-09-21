@extends('layouts.app')

@section('title', 'Home')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    @php
        $categoryImage = fn (string $category) => optional($galleryImages->firstWhere('category', $category))->image;
        $categoryCards = [
            ['route' => 'events', 'image' => $categoryImage('Events'), 'icon' => 'bi-stars', 'label' => 'Event', 'description' => 'Decor & styling for every celebration'],
            ['route' => 'gift.design', 'image' => $categoryImage('Gift & Design'), 'icon' => 'bi-gift', 'label' => 'Gift & Design', 'description' => 'Curated gifts, personalised with care'],
            ['route' => 'laser.work', 'image' => $categoryImage('Laser Work'), 'icon' => 'bi-box', 'label' => 'Laser Design', 'description' => 'Precision engraving on wood & more'],
        ];
    @endphp

    <div class="hb-original">
        <section class="hb-original__hero" id="home">
            <div class="hb-original__hero-shapes" aria-hidden="true"><span>⬡</span><span>⬡</span><span>⬡</span><span>⬡</span></div>
            <div class="container"><div class="hb-original__hero-content"><span class="hb-original__eyebrow">Gifts &amp; Designs</span><h1>Sweet Moments, <span>Handcrafted</span> Just For You</h1><p>HoneyBee Gifts &amp; Designs brings your ideas to life — from personalised gifts and frames to unforgettable event decor and precision laser engraving. One studio, endless creativity.</p></div></div>
            <a href="#services" class="hb-original__scroll" aria-label="Explore services"><i class="bi bi-chevron-down"></i></a>
        </section>

        <section class="hb-original__category-section" id="services"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">What We Do</span><h2>Explore Our Categories</h2><p>Three specialties, one studio — pick a path to see what we can create for you.</p></div><div class="hb-original__category-band">
            @foreach($categoryCards as $card)
                <a href="{{ route($card['route']) }}" class="hb-original__category-panel">
                    @if($card['image'])<img src="{{ asset('storage/' . $card['image']) }}" alt="{{ $card['label'] }}" onerror="this.style.display='none'">@endif
                    <span class="hb-original__category-fallback" aria-hidden="true"></span><span class="hb-original__category-overlay"></span>
                    <span class="hb-original__category-content"><span class="hb-original__category-icon"><i class="bi {{ $card['icon'] }}"></i></span><span class="hb-original__category-label">{{ $card['label'] }}</span><span class="hb-original__category-description">{{ $card['description'] }}</span></span>
                </a>
            @endforeach
        </div></div></section>

        @if($promotions->isNotEmpty())
            <section class="hb-original__offers" id="offers"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Limited Time</span><h2>Special Offers &amp; Promotions</h2><p>Sweet deals across gifts, events, and laser work — while stocks and slots last.</p></div>
                <div class="hb-original__offers-grid">
                    @foreach($promotions as $promotion)
                        <a href="{{ route($promotion->destinationRoute()) }}" class="hb-original__offer-card">
                            <span class="hb-original__ribbon">{{ $promotion->button_text ?: 'Shop Now' }}</span>
                            <div class="hb-original__offer-image">@if($promotion->image)<img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" onerror="this.style.display='none'">@endif<span>{{ $promotion->title }}</span></div>
                            <div class="hb-original__offer-body"><h3>{{ $promotion->title }}</h3><p>{{ $promotion->short_description ?: 'Limited Time Only' }}</p><div class="hb-original__price"><strong>{{ $promotion->button_text ?: 'Shop Now' }}</strong></div><span class="hb-original__offer-button">{{ $promotion->button_text ?: 'Shop Now' }}</span></div>
                        </a>
                    @endforeach
                </div>
            </div></section>
        @else
            <section class="hb-original__offers" id="offers"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Limited Time</span><h2>Special Offers &amp; Promotions</h2><p>Sweet deals across gifts, events, and laser work — while stocks and slots last.</p></div>
                @if($featuredItems->isNotEmpty())<div class="hb-original__offers-grid">
                    @foreach($featuredItems->take(3) as $item)
                        @php $discount = $item['has_offer_price'] ? $item['discount_percentage'] : null; @endphp
                        <a href="{{ route($item['route']) }}" class="hb-original__offer-card">
                            @if($discount)<span class="hb-original__ribbon">{{ $discount }}% Off</span>@else<span class="hb-original__ribbon">Featured</span>@endif
                            <div class="hb-original__offer-image">@if($item['image'])<img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" onerror="this.style.display='none'">@endif<span>{{ $item['type'] }}</span></div>
                            <div class="hb-original__offer-body"><h3>{{ $item['name'] }}</h3><p>{{ $item['type'] }} created by HoneyBee.</p>@if($discount)<div class="hb-original__price"><del>Rs. {{ number_format($item['price'], 2) }}</del><strong>Rs. {{ number_format($item['selling_price'], 2) }}</strong></div>@elseif($item['price'])<div class="hb-original__price"><strong>Rs. {{ number_format($item['selling_price'], 2) }}</strong></div>@endif<span class="hb-original__offer-button">Claim Offer</span></div>
                        </a>
                    @endforeach
                </div>@else<p class="hb-original__empty">New HoneyBee creations will appear here soon.</p>@endif
            </div></section>
        @endif

        <section class="hb-original__gallery-section" id="gallery"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Our Work</span><h2>Gallery</h2><p>A glimpse into our gifts, events, and laser craftsmanship.</p></div>
            @if($galleryImages->isNotEmpty())<div class="hb-original__gallery-grid">@foreach($galleryImages->take(6) as $galleryImage)<figure class="hb-original__gallery-item"><img src="{{ asset('storage/' . $galleryImage->image) }}" alt="{{ $galleryImage->title ?? $galleryImage->category . ' creation' }}" onerror="this.closest('figure').style.display='none'"><figcaption>{{ $galleryImage->title ?? $galleryImage->category }}</figcaption></figure>@endforeach</div>@else<p class="hb-original__empty">Our latest work will be added here soon.</p>@endif
        </div></section>

        <section class="hb-original__contact" id="contact"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Get In Touch</span><h2>Contact Us</h2><p>Have a question or ready to place an order? Reach out — we'd love to hear from you.</p></div><div class="hb-original__contact-grid"><a href="{{ route('contact') }}" class="hb-original__contact-item"><span><i class="bi bi-telephone"></i></span><h3>Phone</h3><p>Contact our HoneyBee team</p></a><a href="{{ route('contact') }}" class="hb-original__contact-item"><span><i class="bi bi-envelope"></i></span><h3>Email</h3><p>Send us an enquiry</p></a><a href="{{ route('contact') }}" class="hb-original__contact-item"><span><i class="bi bi-chat-dots"></i></span><h3>Message Us</h3><p>Start a custom request</p></a></div><div class="hb-original__contact-action"><a href="{{ route('contact') }}">Message Us</a></div></div></section>
    </div>
@endsection

@section('homepage-footer')
<footer class="hb-original-footer"><div class="container"><div class="hb-original-footer__inner"><div class="hb-original-footer__brand"><div><img src="{{ asset('images/logo.png') }}" alt="HoneyBee Logo"><span>Honey<span>Bee</span></span></div><p>Gifts, events &amp; laser work crafted with care — bringing your ideas to life, one detail at a time.</p></div><div><h3>Explore</h3><a href="{{ route('home') }}">Home</a><a href="{{ route('gift.design') }}">Gift &amp; Design</a><a href="{{ route('events') }}">Events</a><a href="{{ route('laser.work') }}">Laser Work</a></div><div><h3>Company</h3><a href="#offers">Offers</a><a href="#gallery">Gallery</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div><div><h3>Contact</h3><a href="{{ route('contact') }}"><i class="bi bi-envelope"></i> Send an enquiry</a><a href="{{ route('contact') }}"><i class="bi bi-chat-dots"></i> Message HoneyBee</a></div></div><p class="hb-original-footer__bottom">&copy; {{ now()->year }} HoneyBee Gifts <span>&amp;</span> Designs. All rights reserved.</p></div></footer>
@endsection
