@extends('layouts.app')

@section('title', 'Gift & Design')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/gift-design.css') }}">
@endpush

@section('content')
<section class="hero" id="home">
  <div class="hero-bg-overlay"></div>
  <div class="container hero-content">
    <p class="hero-eyebrow">Available for all island delivery</p>
    <h1 class="hero-heading">Creative Gifts &amp; Designs<br><span class="hero-accent">for Every Occasion</span></h1>
    <p class="hero-sub">Personalised gifts, ready-made frames, and custom frame work made for your most meaningful moments.</p>
    <div class="hero-ctas">
      <a href="#gifts" class="btn btn-primary">Explore Gifts</a>
      <a href="#frames" class="btn btn-outline-light">Explore Frames</a>
    </div>
  </div>
  <div class="hero-scroll-hint">down</div>
</section>

<section class="custom-frame-section" id="custom-frame-work">
  <div class="container custom-frame-inner">
    <div>
      <p class="section-eyebrow">Made for You</p>
      <h2 class="section-title">Custom Frame Work Available</h2>
      <p>Share your photo, preferred size, colours, message, and frame style. We will create a frame that is uniquely yours.</p>
    </div>
    <div class="custom-frame-actions">
      <button class="btn btn-primary" onclick="requestCustomFrame()" type="button">Request a Custom Frame</button>
      <button class="btn btn-whatsapp" onclick="whatsappOrder('Custom Frame Work')" type="button">WhatsApp Us</button>
    </div>
  </div>
</section>

<section class="search-bar-section">
  <div class="container">
    <div class="search-wrap">
      <input type="text" id="search-input" placeholder="Search gifts, frames, designs..." oninput="searchProducts()">
      <span class="search-icon"><i class="bi bi-search"></i></span>
    </div>
    <div id="search-results" class="search-results hidden"></div>
  </div>
</section>

<section class="products-section" id="gifts">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">Category</p>
      <h2 class="section-title">Our Gift Collection</h2>
      <p class="section-sub">Thoughtfully curated gifts for every milestone and memory.</p>
    </div>
    <div class="products-grid" id="gifts-grid">
      @forelse($gifts as $gift)
        @php
          $price = $gift->offer_price ?? $gift->price;
          $image = $gift->image ? asset('storage/' . $gift->image) : null;
          $message = rawurlencode('Hi! I want to order ' . $gift->item_name . ' - Rs.' . $price);
          $customizationMessage = rawurlencode("Hello HoneyBee, I would like to discuss customization for:\nProduct: {$gift->item_name}\nCategory: Gift\nPrice: Rs. {$price}\nI would like to customize: ");
        @endphp
        <div class="product-card" data-name="{{ e($gift->item_name) }}" data-category="{{ e($gift->category) }}">
          <div class="product-img-wrap">
            @if($image)
              <img src="{{ $image }}" alt="{{ $gift->item_name }}">
            @else
              <div class="h-100 d-flex align-items-center justify-content-center text-muted">Image coming soon</div>
            @endif
            @if($gift->offer_price)
              <span class="badge badge-honey">Offer</span>
            @endif
          </div>
          <div class="product-body">
            <h3>{{ $gift->item_name }}</h3>
            <p>{{ $gift->description ?: $gift->category }}</p>
            <p class="price">
              @if($gift->offer_price)
                <del>Rs {{ number_format($gift->price, 2) }}</del>
                <strong>Rs {{ number_format($gift->offer_price, 2) }}</strong>
              @else
                From <strong>Rs {{ number_format($gift->price, 2) }}</strong>
              @endif
            </p>
            <div class="product-actions">
              <button
                  class="btn btn-cart-add"
                  onclick="addToCart('{{ addslashes($gift->item_name) }}', {{ (float) $price }}, {{ $gift->gift_design_id }}, 'gift')"
                  type="button">
                  Add
              </button>
              <button class="btn btn-details" onclick="viewDetails('{{ addslashes($gift->item_name) }}')" type="button">Details</button>
              <a class="btn btn-wa" href="https://wa.me/94767158873?text={{ $message }}" target="_blank" rel="noopener">WhatsApp</a>
            </div>
            <p class="customization-prompt">Need Customization? <a href="https://wa.me/94767158873?text={{ $customizationMessage }}" target="_blank" rel="noopener">Discuss with Admin</a></p>
          </div>
        </div>
      @empty
        <p class="text-center">Gift products will be added here soon.</p>
      @endforelse
    </div>
  </div>
</section>

<section class="products-section products-section--dark" id="frames">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">Category</p>
      <h2 class="section-title">Frame Collection</h2>
      <p class="section-sub">Quality frames crafted to hold your most precious moments.</p>
    </div>
    <div class="products-grid" id="frames-grid">
      @forelse($frames as $frame)
        @php
          $price = $frame->offer_price ?? $frame->price;
          $image = $frame->image ? asset('storage/' . $frame->image) : null;
          $message = rawurlencode('Hi! I want to order ' . $frame->item_name . ' - Rs.' . $price);
          $customizationMessage = rawurlencode("Hello HoneyBee, I would like to discuss customization for:\nProduct: {$frame->item_name}\nCategory: Frame\nPrice: Rs. {$price}\nI would like to customize: ");
        @endphp
        <div class="product-card" data-name="{{ e($frame->item_name) }}" data-category="frame">
          <div class="product-img-wrap">
            @if($image)
              <img src="{{ $image }}" alt="{{ $frame->item_name }}">
            @else
              <div class="h-100 d-flex align-items-center justify-content-center text-muted">Image coming soon</div>
            @endif
            @if($frame->offer_price)<span class="badge badge-honey">Offer</span>@endif
          </div>
          <div class="product-body">
            <h3>{{ $frame->item_name }}</h3>
            <p>{{ $frame->description ?: $frame->category }}</p>
            <p class="price">
              @if($frame->offer_price)
                <del>Rs {{ number_format($frame->price, 2) }}</del>
                <strong>Rs {{ number_format($frame->offer_price, 2) }}</strong>
              @else
                From <strong>Rs {{ number_format($frame->price, 2) }}</strong>
              @endif
            </p>
            <div class="product-actions">
              <button
                  class="btn btn-cart-add"
                  onclick="addToCart('{{ addslashes($frame->item_name) }}', {{ (float) $price }}, {{ $frame->gift_design_id }}, 'frame')"
                  type="button">
                  Add
              </button>
              <button class="btn btn-details" onclick="viewDetails('{{ addslashes($frame->item_name) }}')" type="button">Details</button>
              <a class="btn btn-wa" href="https://wa.me/94767158873?text={{ $message }}" target="_blank" rel="noopener">WhatsApp</a>
            </div>
            <p class="customization-prompt">Need Customization? <a href="https://wa.me/94767158873?text={{ $customizationMessage }}" target="_blank" rel="noopener">Discuss with Admin</a></p>
          </div>
        </div>
      @empty
        <p class="text-center">Frame products will be added here soon.</p>
      @endforelse
    </div>
  </div>
</section>

<section class="gallery-section frame-gallery-section" id="frame-designs">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">Frame Category</p>
      <h2 class="section-title">Frame Design Gallery</h2>
      <p class="section-sub">Browse our frame designs for inspiration, then personalise one to suit your occasion.</p>
    </div>
    @if($frameDesignImages->isNotEmpty())
      <div class="gallery-grid">
        @foreach($frameDesignImages as $frameDesignImage)
          <figure class="gallery-item m-0">
            <img src="{{ asset('storage/' . $frameDesignImage->image) }}" alt="{{ $frameDesignImage->title ?? 'Frame design' }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.closest('figure').style.display='none'">
            @if($frameDesignImage->title)
              <figcaption class="gallery-overlay">{{ $frameDesignImage->title }}</figcaption>
            @endif
          </figure>
        @endforeach
      </div>
    @else
      <p class="text-center mb-0">Frame design pictures will be added here soon.</p>
    @endif
  </div>
</section>

<section class="services-section" id="services">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">What We Do</p>
      <h2 class="section-title">Our Services</h2>
    </div>
    <div class="services-grid">
      <div class="service-card"><div class="service-icon">Design</div><h3>Custom Design Creation</h3><p>We turn your ideas into unique finished products.</p></div>
      <div class="service-card"><div class="service-icon">Cut</div><h3>Precision Gift Cutting</h3><p>Clean cutting for acrylic, wood, and paper crafts.</p></div>
      <div class="service-card"><div class="service-icon">Gift</div><h3>Personalised Products</h3><p>Names, dates, photos, and messages tailored to your story.</p></div>
      <div class="service-card"><div class="service-icon">Ship</div><h3>Fast Delivery</h3><p>Local delivery and island-wide shipping options.</p></div>
    </div>
  </div>
</section>

<section class="gallery-section" id="gallery">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">Our Work</p>
      <h2 class="section-title">Gallery</h2>
      <p class="section-sub">A selection of our latest personalised gifts and designs.</p>
    </div>
    @if($giftGalleryImages->isNotEmpty())
      <div class="gallery-grid">
        @foreach($giftGalleryImages as $galleryImage)
          <figure class="gallery-item m-0">
            <img src="{{ asset('storage/' . $galleryImage->image) }}" alt="{{ $galleryImage->title ?? 'Gift and Design gallery image' }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.closest('figure').style.display='none'">
            @if($galleryImage->title)
              <figcaption class="gallery-overlay">{{ $galleryImage->title }}</figcaption>
            @endif
          </figure>
        @endforeach
      </div>
    @else
      <p class="text-center mb-0">Our latest creations will be added here soon.</p>
    @endif
  </div>
</section>

<section class="section contact-section" id="contact">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">Find Us</p>
      <h2 class="section-title">Contact Gift and Design</h2>
    </div>
    <div class="contact-grid">
      <div class="contact-item"><h4>Phone</h4><p>+94 767158873</p><p>+94 707159988</p></div>
      <div class="contact-item"><h4>Email</h4><p><a href="mailto:Hoheybeedestgns99@gmail.com">Hoheybeedestgns99@gmail.com</a></p></div>
      <div class="contact-item"><h4>Address</h4><p>190, Sir. Pom Ramanathan Road,<br>Thirunelvely, Jaffna</p></div>
      <div class="contact-item"><h4>Social Media</h4><div class="social-links"><a href="#" class="social-icon social-facebook"><i class="bi bi-facebook"></i></a><a href="#" class="social-icon social-instagram"><i class="bi bi-instagram"></i></a><a href="https://wa.me/94767158873" class="social-icon social-whatsapp" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i></a></div></div>
    </div>
  </div>
</section>

<section class="inquiry-section" id="inquiry">
  <div class="container inquiry-inner">
    <div class="inquiry-copy">
      <p class="section-eyebrow">Get in Touch</p>
      <h2 class="section-title">Place Your Order</h2>
      <p>Drop us a message or reach out on WhatsApp. We respond as soon as possible.</p>
      <div class="inquiry-ctas">
        <button class="btn btn-primary" onclick="document.getElementById('inquiry-form').scrollIntoView({behavior:'smooth'})" type="button">Order Now</button>
        <button class="btn btn-whatsapp" onclick="whatsappOrder('General Inquiry')" type="button">WhatsApp Us</button>
      </div>
    </div>
    <form class="inquiry-form" id="inquiry-form" onsubmit="submitForm(event)">
      <h3>Send an Enquiry</h3>
      <div class="form-row"><div class="form-group"><label>Full Name</label><input type="text" required></div><div class="form-group"><label>Phone / WhatsApp</label><input type="tel" required></div></div>
      <div class="form-group"><label>Email Address</label><input type="email"></div>
      <div class="form-group"><label>Product / Service</label><select id="inquiry-product">@foreach($gifts as $gift)<option>{{ $gift->item_name }}</option>@endforeach @foreach($frames as $frame)<option>{{ $frame->item_name }}</option>@endforeach<option>Customized Gifts</option><option>Custom Frame Work</option></select></div>
      <div class="form-group"><label>Message</label><textarea rows="4" placeholder="Tell us what you need..."></textarea></div>
      <button type="submit" class="btn btn-primary btn-full">Send Enquiry</button>
    </form>
  </div>
</section>


<aside class="cart-sidebar" id="cart-sidebar">
  <div class="cart-header"><h3>Your Cart</h3><button onclick="toggleCart()" class="cart-close" type="button">Close</button></div>
  <div class="cart-items" id="cart-items"><p class="cart-empty">Your cart is empty.</p></div>
  <div class="cart-footer"><p class="cart-total">Total: <strong id="cart-total">Rs 0</strong></p><button class="btn btn-primary btn-full" onclick="proceedToCheckout()" type="button">Proceed to Checkout</button></div>
</aside>
<div class="cart-overlay" id="cart-overlay" onclick="toggleCart()"></div>
<div class="modal-overlay" id="detail-overlay" onclick="closeDetail()"></div>
<div class="detail-modal" id="detail-modal"><button class="admin-close" onclick="closeDetail()" type="button">Close</button><h3 id="detail-title"></h3><p id="detail-body"></p><button class="btn btn-whatsapp" id="detail-wa" type="button">Order via WhatsApp</button></div>
<div class="toast" id="toast"></div>

@push('scripts')
<script>window.honeyBeeCheckoutUrl = @json(request()->getBaseUrl() . '/checkout');</script>
<script src="{{ request()->getBaseUrl() }}/js/gift-design.js"></script>
@endpush

@endsection
