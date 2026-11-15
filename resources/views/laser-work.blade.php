<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
  <title>Laser Works - HoneyBee</title>
  <meta name="description" content="Explore HoneyBee custom laser work, personalized engraving, and handcrafted designs made for thoughtful gifts.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/leser.css') }}">
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
<header class="header" id="header">
  <div class="header-inner container">
    <a href="{{ route('home') }}" class="brand">
      <img src="{{ asset('images/logo.webp') }}" alt="Laser Works Logo" class="brand-logo">
      <span class="brand-name">HoneyBee<span class="accent"> Shop</span></span>
    </a>
    <nav class="nav" id="nav">
      <ul class="nav-list">
        <li><a href="{{ route('home') }}" class="nav-link">Home</a></li>
        <li><a href="{{ route('gift.design') }}" class="nav-link">Gift &amp; Design</a></li>
        <li><a href="{{ route('laser.work') }}" class="nav-link active">Laser Work</a></li>
        <li><a href="{{ route('events') }}" class="nav-link">Events</a></li>
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
        @php
          $dashboardUser = auth()->user();
          $dashboardStaffRecords = \App\Models\Staff::where('email', $dashboardUser->email)->get(['role']);
          $dashboardCustomerExists = \App\Models\Customer::where('email', $dashboardUser->email)->exists();

          if ($dashboardStaffRecords->count() === 1 && ! $dashboardCustomerExists) {
              $dashboardRole = strtolower((string) $dashboardStaffRecords->first()->role);
              $dashboardRoute = match ($dashboardRole) {
                  'admin' => 'admin.dashboard',
                  'manager' => 'manager.dashboard',
                  'staff' => 'staff.dashboard',
                  default => null,
              };
          } elseif ($dashboardStaffRecords->isEmpty() && $dashboardCustomerExists) {
              $dashboardRoute = 'dashboard';
          } else {
              $dashboardRoute = null;
          }

          $dashboardUrl = $dashboardRoute !== null ? route($dashboardRoute) : null;
        @endphp
        @if($dashboardUrl)<a class="btn btn-login" href="{{ $dashboardUrl }}">Dashboard</a>@endif
      @else
        <a class="btn btn-login" href="{{ route('login') }}">Login</a>
      @endauth
      <button class="hamburger" id="hamburger" aria-label="Open menu" type="button">Menu</button>
    </div>
  </div>
</header>

<section class="hero" id="home">
  <div class="hero-bg-overlay"></div>
  <div class="laser-lines" aria-hidden="true"><span class="laser-line l1"></span><span class="laser-line l2"></span><span class="laser-line l3"></span></div>
  <div class="hero-content container">
    <p class="hero-eyebrow">Available for all island delivery</p>
    <h1 class="hero-heading">Creative Laser Designs<br><span class="accent">for Every Occasion</span></h1>
    <p class="hero-eyebrow">Custom laser engraving and cutting delivered to your door.</p>
  </div>
  <div class="hero-scroll-hint" aria-hidden="true"><i class="fas fa-chevron-down"></i></div>
</section>

<section class="search-section" id="search">
  <div class="container">
    <div class="search-wrap">
      <i class="fas fa-search search-icon"></i>
      <input type="text" id="searchInput" class="search-input" placeholder="Search laser products - e.g. trophy, wedding, keychain..." autocomplete="off" oninput="liveSearch(this.value)" onkeydown="if(event.key==='Enter')performSearch()">
      <button class="btn btn-primary search-btn" onclick="performSearch()" type="button">Search</button>
    </div>
    <div class="search-results" id="searchResults"></div>
  </div>
</section>

<section class="section products-section" id="products">
  <div class="container">
    <div class="section-header">
      <p class="section-eyebrow">What We Create</p>
      <h2 class="section-title">Laser Product Categories</h2>
      <p class="section-sub">Every piece is precision-cut and lovingly engraved. Browse our full range below.</p>
    </div>
    <div class="products-grid" id="productsGrid">
      @forelse($laserWorks as $laserWork)
        @php
          $price = $laserWork->currentSellingPrice();
          $desc = $laserWork->description ?: 'Precision laser-crafted product made just for you. Contact us to discuss sizes, materials, and pricing.';
          $message = rawurlencode('Hello HoneyBee Laser Works, I am interested in ' . $laserWork->product_name . ' - Rs. ' . $price);
          $customizationMessage = rawurlencode("Hello HoneyBee, I would like to discuss customization for:\nProduct: {$laserWork->product_name}\nLaser type: {$laserWork->laser_type}\nMaterial: " . ($laserWork->material_type ?: 'Not specified') . "\nPrice: Rs. {$price}\nMy customization requirement: ");
        @endphp
        <div class="product-card" data-name="{{ e($laserWork->product_name) }}">
          <div class="card-img-wrap">
            @if($imageUrl = \App\Support\PublicImage::url($laserWork->image))
              <img src="{{ $imageUrl }}" alt="{{ $laserWork->product_name }}" class="laser-product-image" loading="lazy">
            @else
              <div class="card-img-placeholder frame-img" aria-label="No product image available"><i class="fas fa-bolt"></i></div>
            @endif
            @if($laserWork->hasValidOfferPrice())<span class="card-badge badge-new">Offer</span>@endif
          </div>
          <div class="card-body">
            <h3 class="card-title">{{ $laserWork->product_name }}</h3>
            <p class="card-desc">{{ $desc }}</p>
            <p class="price">
              @if($laserWork->hasValidOfferPrice())
                <del>Rs {{ number_format((float) $laserWork->price, 2) }}</del>
                <strong>Rs {{ number_format((float) $laserWork->offer_price, 2) }}</strong>
                <span class="badge badge-new">{{ $laserWork->discountPercentage() }}% OFF</span>
              @else
                From <strong>Rs {{ number_format((float) $laserWork->price, 2) }}</strong>
              @endif
            </p>
            @if($laserWork->production_type === 'custom_made')
              <p class="stock-availability text-info">Made to order</p>
            @endif
            <div class="card-actions">
              <a class="btn btn-ghost" href="{{ route('laser.work.show', $laserWork->laser_id) }}">View Details</a>
              <a class="btn btn-primary" href="https://wa.me/{{ config('honeybee.contact.whatsapp_number') }}?text={{ $message }}" target="_blank" rel="noopener noreferrer">Order Now</a>
            </div>
            <p class="customization-prompt">Need Customization? <a href="https://wa.me/{{ config('honeybee.contact.whatsapp_number') }}?text={{ $customizationMessage }}" target="_blank" rel="noopener noreferrer">Discuss with HoneyBee</a></p>
          </div>
        </div>
      @empty
        <p class="catalog-empty">No laser products are available yet. Please check back soon or contact us about a custom request.</p>
      @endforelse
    </div>
  </div>
</section>

<section class="section services-section" id="services">
  <div class="container">
    <div class="section-header"><p class="section-eyebrow">Why Choose Us</p><h2 class="section-title">Our Services</h2></div>
    <div class="services-grid">
      <div class="service-card"><div class="service-icon"><i class="fas fa-pen-nib"></i></div><h3>Custom Design Creation</h3><p>Our designers turn your vision into a precise laser-ready file.</p></div>
      <div class="service-card"><div class="service-icon"><i class="fas fa-cut"></i></div><h3>Precision Laser Cutting</h3><p>Complex shapes, fine lettering, and patterns done cleanly.</p></div>
      <div class="service-card"><div class="service-icon"><i class="fas fa-layer-group"></i></div><h3>Laser Engraving</h3><p>Permanent engraving on wood, acrylic, leather, slate, and metal.</p></div>
      <div class="service-card"><div class="service-icon"><i class="fas fa-truck-fast"></i></div><h3>Fast Delivery</h3><p>Express turnaround options across Jaffna District.</p></div>
    </div>
  </div>
</section>

<section class="section gallery-section" id="gallery">
  <div class="container">
    <div class="section-header"><p class="section-eyebrow">Our Portfolio</p><h2 class="section-title">Gallery</h2><p class="section-sub">A glimpse of what leaves our workshop every week.</p></div>
    <div class="gallery-grid" id="galleryGrid">
      @forelse($galleryImages as $galleryImage)
        <figure class="gallery-item m-0">
          @if($imageUrl = \App\Support\PublicImage::url($galleryImage->image))<img src="{{ $imageUrl }}" alt="{{ $galleryImage->title ?? 'Laser Work gallery image' }}" class="w-100 h-100" style="display: block; object-fit: cover;">@else<span class="gallery-image-placeholder" style="width:100%;height:100%;display:grid;place-items:center;">Laser gallery image unavailable</span>@endif
        </figure>
      @empty
        <p class="mb-0">Our latest laser work will be added here soon.</p>
      @endforelse
    </div>
  </div>
</section>

<section class="section inquiry-section" id="inquiry">
  <div class="container">
    <div class="inquiry-layout">
      <div class="inquiry-cta-col">
        <p class="section-eyebrow">Ready to Order?</p>
        <h2 class="section-title">Let's Create<br>Something Special</h2>
        <p class="inquiry-intro">We engrave memories, milestones, and brands with care and precision.</p>
        <div class="inquiry-btns"><button class="btn btn-primary" onclick="scrollToForm()" type="button"><i class="fas fa-file-alt"></i> Order Now</button><a href="https://wa.me/{{ config('honeybee.contact.whatsapp_number') }}?text=Hello%2C%20I%27m%20interested%20in%20Laser%20Works%20products!" target="_blank" class="btn btn-whatsapp" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Inquiry</a></div>
      </div>
      <div class="inquiry-form-col" id="contactForm">
        <div class="form-card">
          <h3 class="form-title">Send an Inquiry</h3>
          <div class="form-group"><label for="fname">Full Name</label><input type="text" id="fname"></div>
          <div class="form-group"><label for="fphone">Phone Number</label><input type="tel" id="fphone"></div>
          <div class="form-group"><label for="femail">Email Address</label><input type="email" id="femail"></div>
          <div class="form-group"><label for="fproduct">Product Interest</label><select id="fproduct"><option value="">Select a product...</option>@foreach($laserWorks as $laserWork)<option>{{ $laserWork->product_name }}</option>@endforeach<option>Customized Laser Cut Designs</option></select></div>
          <div class="form-group"><label for="fmessage">Message / Requirements</label><textarea id="fmessage" rows="4"></textarea></div>
          <button class="btn btn-primary btn-full" onclick="submitInquiry()" type="button"><i class="fas fa-paper-plane"></i> Send Inquiry</button>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section contact-section" id="contact">
  <div class="container">
    <div class="section-header"><p class="section-eyebrow">Find Us</p><h2 class="section-title">Contact Laser Works</h2></div>
    <div class="contact-grid">
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-phone-alt"></i></div><h4>Phone</h4><p><a href="tel:+{{ config('honeybee.contact.whatsapp_number') }}">{{ config('honeybee.contact.phone_local') }}</a></p></div>
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-envelope"></i></div><h4>Email</h4><p>Honey beelaserworks @gmail.com.</p></div>
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div><h4>Address</h4><p>190, Sir. Pom Ramanathan Road,<br>Thirunelvely, Jaffna</p></div>
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-share-alt"></i></div><h4>Social Media</h4><div class="social-links"><a href="#" class="social-icon fb"><i class="fab fa-facebook-f"></i></a><a href="#" class="social-icon ig"><i class="fab fa-instagram"></i></a><a href="https://wa.me/{{ config('honeybee.contact.whatsapp_number') }}" class="social-icon wa" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a></div></div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container footer-inner">
    <div class="footer-brand"><span class="brand-name footer-brand-name">Laser<span class="accent">Works</span></span><p>Precision laser engraving and cutting for every occasion.</p></div>
    <div class="footer-col"><h5>Quick Links</h5><ul><li><a href="#home">Home</a></li><li><a href="#products">Laser Products</a></li><li><a href="#inquiry">Order Now</a></li><li><a href="#contact">Contact</a></li></ul></div>
    <div class="footer-col"><h5>Contact Info</h5><ul class="footer-contact"><li><i class="fas fa-phone-alt"></i> <a href="tel:+{{ config('honeybee.contact.whatsapp_number') }}">{{ config('honeybee.contact.phone_local') }}</a></li><li><i class="fas fa-envelope"></i> Honey beelaserworks @gmail.com.</li><li><i class="fas fa-map-marker-alt"></i> 190, Sir. Pom Ramanathan Road, Thirunelvely, Jaffna</li></ul></div>
  </div>
  <div class="footer-bottom"><div class="container"><p>&copy; 2025 Laser Works. All rights reserved.</p></div></div>
</footer>

<div class="modal-overlay" id="productModal">
  <div class="detail-modal">
    <button class="admin-close" onclick="closeProductModal()" type="button"><i class="fas fa-times"></i></button>
    <div class="detail-img-wrap"><div class="detail-img-placeholder" id="productModalImg"></div></div>
    <div class="detail-body"><h2 id="productModalTitle"></h2><p id="productModalDesc"></p><button class="btn btn-primary" onclick="closeProductModal(); scrollToForm()" type="button">Order Now</button></div>
  </div>
</div>
<div class="toast" id="toast" role="alert" aria-live="polite"></div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script>window.honeyBeeContact = { whatsappNumber: @json(config('honeybee.contact.whatsapp_number')) };</script>
<script src="{{ asset('js/leser.js') }}"></script>
</body>
</html>
