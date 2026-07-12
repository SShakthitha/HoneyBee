<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HoneyBee - Gifts & Designs</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/gift-design.css') }}">
</head>
<body>
<header class="header" id="header">
  <div class="header-inner container">
    <a href="{{ route('home') }}" class="brand">
      <img src="{{ asset('images/logo.png') }}" alt="HoneyBee logo" class="brand-logo">
      <span class="brand-name">HoneyBee <span class="amp">Shop</span></span>
    </a>
    <nav id="main-nav">
      <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('gift.design') }}" class="active">Gift &amp; Design</a></li>
        <li><a href="{{ route('laser.work') }}">Laser Work</a></li>
        <li><a href="{{ route('events') }}">Events</a></li>
      </ul>
    </nav>
    <div class="header-actions">
      <button class="btn btn-cart" onclick="toggleCart()" type="button">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="20" r="1"></circle>
            <circle cx="18" cy="20" r="1"></circle>
            <path d="M1 1h4l2.68 13.39A2 2 0 0 0 9.64 16H19a2 2 0 0 0 2-1.64L23 6H6"></path>
        </svg>
        <span id="cart-count">0</span>
      </button>
      @auth
        <a class="btn btn-login" href="{{ route('dashboard') }}">Dashboard</a>
      @else
        <a class="btn btn-login" href="{{ route('login') }}">Login</a>
      @endauth
      <button class="hamburger" id="hamburger" aria-label="Open menu" type="button">Menu</button>
    </div>
  </div>
</header>

<section class="hero" id="home">
  <div class="hero-bg-overlay"></div>
  <div class="container hero-content">
    <p class="hero-eyebrow">Available for all island delivery</p>
    <h1 class="hero-heading">Creative Gifts &amp; Designs<br><span class="hero-accent">for Every Occasion</span></h1>
    <p class="hero-sub">Handcrafted custom frames, personalised gifts, and one-of-a-kind designs delivered with care.</p>
    <div class="hero-ctas">
      <a href="#gifts" class="btn btn-primary">Explore Gifts</a>
      <a href="#inquiry" class="btn btn-outline-light">Order Now</a>
    </div>
  </div>
  <div class="hero-scroll-hint">down</div>
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
          $image = $gift->image ? asset('images/gifts/' . $gift->image) : null;
          $message = rawurlencode('Hi! I want to order ' . $gift->item_name . ' - Rs.' . $price);
        @endphp
        <div class="product-card" data-name="{{ e($gift->item_name) }}" data-category="{{ e($gift->category) }}">
          <div class="product-img-wrap">
            @if($image)
              <img src="{{ $image }}" alt="{{ $gift->item_name }}">
            @else
              <img src="https://images.unsplash.com/photo-1513201099705-a9746e1e201f?w=600&q=80" alt="{{ $gift->item_name }}">
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
              <button class="btn btn-cart-add" onclick="addToCart('{{ addslashes($gift->item_name) }}', {{ (float) $price }})" type="button">Add</button>
              <button class="btn btn-details" onclick="viewDetails('{{ addslashes($gift->item_name) }}')" type="button">Details</button>
              <a class="btn btn-wa" href="https://wa.me/94767158873?text={{ $message }}" target="_blank" rel="noopener">WhatsApp</a>
            </div>
          </div>
        </div>
      @empty
        @foreach([
          ['Birthday Gifts', 'Vibrant personalised boxes, cake toppers, and custom hampers.', 1500, 'https://images.unsplash.com/photo-1513201099705-a9746e1e201f?w=600&q=80'],
          ['Wedding Gifts', 'Elegant keepsake sets, engraved trays, and couple frames.', 3500, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=600&q=80'],
          ['Anniversary Gifts', 'Custom photo books, heart frames, and memory boxes.', 2800, 'https://images.unsplash.com/photo-1518199266791-5375a83190b7?w=600&q=80'],
          ['Customized Gifts', 'Mugs, cushions, keychains, and more with your own design.', 1200, 'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=600&q=80'],
        ] as $sample)
          <div class="product-card" data-name="{{ $sample[0] }}" data-category="gift">
            <div class="product-img-wrap"><img src="{{ $sample[3] }}" alt="{{ $sample[0] }}"></div>
            <div class="product-body">
              <h3>{{ $sample[0] }}</h3>
              <p>{{ $sample[1] }}</p>
              <p class="price">From <strong>Rs {{ number_format($sample[2]) }}</strong></p>
              <div class="product-actions">
                <button class="btn btn-cart-add" onclick="addToCart('{{ $sample[0] }}', {{ $sample[2] }})" type="button">Add</button>
                <button class="btn btn-details" onclick="viewDetails('{{ $sample[0] }}')" type="button">Details</button>
                <button class="btn btn-wa" onclick="whatsappOrder('{{ $sample[0] }}')" type="button">WhatsApp</button>
              </div>
            </div>
          </div>
        @endforeach
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
    <div class="products-grid">
      @foreach([
        ['Photo Frames', 'Classic and modern photo frames in multiple sizes.', 800, 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=600&q=80'],
        ['Wedding Frames', 'Ornate frames and collage boards for your big day photos.', 2500, 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=600&q=80'],
        ['Wooden Frames', 'Solid wood frames with laser-engraved names or messages.', 1500, 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=600&q=80'],
      ] as $frame)
        <div class="product-card" data-name="{{ $frame[0] }}" data-category="frame">
          <div class="product-img-wrap"><img src="{{ $frame[3] }}" alt="{{ $frame[0] }}"></div>
          <div class="product-body">
            <h3>{{ $frame[0] }}</h3>
            <p>{{ $frame[1] }}</p>
            <p class="price">From <strong>Rs {{ number_format($frame[2]) }}</strong></p>
            <div class="product-actions">
              <button class="btn btn-cart-add" onclick="addToCart('{{ $frame[0] }}', {{ $frame[2] }})" type="button">Add</button>
              <button class="btn btn-details" onclick="viewDetails('{{ $frame[0] }}')" type="button">Details</button>
              <button class="btn btn-wa" onclick="whatsappOrder('{{ $frame[0] }}')" type="button">WhatsApp</button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
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
      <div class="form-group"><label>Product / Service</label><select>@foreach($gifts as $gift)<option>{{ $gift->item_name }}</option>@endforeach<option>Customized Gifts</option><option>Photo Frames</option></select></div>
      <div class="form-group"><label>Message</label><textarea rows="4" placeholder="Tell us what you need..."></textarea></div>
      <button type="submit" class="btn btn-primary btn-full">Send Enquiry</button>
    </form>
  </div>
</section>

<footer class="footer">
  <div class="container footer-inner">
    <div class="footer-brand"><span class="brand-name footer-brand-name">Gift<span class="accent">Design</span></span><p>Personalized gifts and design work proudly serving Jaffna.</p></div>
    <div class="footer-col"><h5>Quick Links</h5><ul><li><a href="#home">Home</a></li><li><a href="#gifts">Gift and Design</a></li><li><a href="#inquiry">Order Now</a></li><li><a href="#contact">Contact</a></li></ul></div>
    <div class="footer-col"><h5>Contact Info</h5><ul class="footer-contact"><li>+94 767158873 / +94 707159988</li><li><a href="mailto:Hoheybeedestgns99@gmail.com">Hoheybeedestgns99@gmail.com</a></li><li>190, Sir. Pom Ramanathan Road, Thirunelvely, Jaffna</li></ul></div>
  </div>
  <div class="footer-bottom"><div class="container"><p>&copy; 2025 Gift and Design. All rights reserved.</p></div></div>
</footer>

<aside class="cart-sidebar" id="cart-sidebar">
  <div class="cart-header"><h3>Your Cart</h3><button onclick="toggleCart()" class="cart-close" type="button">Close</button></div>
  <div class="cart-items" id="cart-items"><p class="cart-empty">Your cart is empty.</p></div>
  <div class="cart-footer"><p class="cart-total">Total: <strong id="cart-total">Rs 0</strong></p><button class="btn btn-whatsapp btn-full" onclick="checkoutWhatsapp()" type="button">Order via WhatsApp</button></div>
</aside>
<div class="cart-overlay" id="cart-overlay" onclick="toggleCart()"></div>
<div class="modal-overlay" id="detail-overlay" onclick="closeDetail()"></div>
<div class="detail-modal" id="detail-modal"><button class="admin-close" onclick="closeDetail()" type="button">Close</button><h3 id="detail-title"></h3><p id="detail-body"></p><button class="btn btn-whatsapp" id="detail-wa" type="button">Order via WhatsApp</button></div>
<div class="toast" id="toast"></div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/gift-design.js') }}"></script>
</body>
</html>
