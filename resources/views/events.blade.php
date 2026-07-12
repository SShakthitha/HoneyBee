<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - HoneyBee</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/event.css') }}">
</head>
<body>
<header class="header" id="header">
  <div class="header-inner">
    <a class="logo-wrap" href="{{ route('home') }}">
      <img src="{{ asset('images/logo.png') }}" alt="HoneyBee Gifts & Designs Logo" class="logo-img">
      <div class="logo-text"><span class="brand-name">HoneyBee<span class="accent"> Shop</span></span></div>
    </a>
    <nav class="nav" id="nav">
      <a href="{{ route('home') }}" class="nav-link">Home</a>
      <a href="{{ route('gift.design') }}" class="nav-link">Gift &amp; Design</a>
      <a href="{{ route('laser.work') }}" class="nav-link">Laser Work</a>
      <a href="{{ route('events') }}" class="nav-link active">Events</a>
    </nav>
    <div class="header-actions">
      <button class="btn btn-cart" onclick="showToast('Choose an event package to get started.')" type="button">
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
      <button class="hamburger" id="hamburger" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="nav"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>

<section class="hero" id="home">
  <div class="hero-bg-pattern"></div>
  <div class="hero-content">
    <p class="hero-eyebrow">Available for Jaffna District</p>
    <h1 class="hero-heading">Creative Event Designs<br><span class="accent">for Every Occasion</span></h1>
    <p class="hero-eyebrow">Memorable gatherings with thoughtful design, meaningful moments, and smooth planning.</p>
  </div>
  <div class="hero-scroll-hint"><span>Scroll to explore</span><i class="fas fa-chevron-down"></i></div>
</section>

<section class="search-section" id="search">
  <div class="container">
    <div class="search-wrap">
      <i class="fas fa-search search-icon"></i>
      <input type="text" id="searchInput" placeholder="Search event categories... e.g. Wedding, Birthday" class="search-input" oninput="filterEvents()">
      <button class="search-clear" onclick="clearSearch()" id="clearBtn" style="display:none" type="button"><i class="fas fa-times"></i></button>
    </div>
    <p class="search-hint" id="searchHint"></p>
  </div>
</section>

<section class="events-section" id="events">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">What We Do</span>
      <h2 class="section-title">Event Categories</h2>
      <p class="section-desc">From intimate gatherings to grand celebrations, we design every moment with care.</p>
    </div>
    <div class="events-grid" id="eventsGrid">
      @forelse($events as $event)
        @php
          $price = $event->offer_price ?? $event->price;
          $desc = $event->description ?: 'Custom event planning and decoration for your special day.';
          $message = rawurlencode('Hello HoneyBee Events! I would like to book ' . $event->event_name . ' - Rs.' . $price);
        @endphp
        <div class="event-card reveal visible" data-name="{{ strtolower($event->event_name . ' ' . $event->event_type . ' ' . $desc) }}">
          <div class="event-card-img" style="background:linear-gradient(135deg,#f5a623,#ff6b6b)"><span><i class="fas fa-calendar-star"></i></span></div>
          <div class="event-card-body">
            <span class="event-card-tag">{{ $event->event_type }}</span>
            <h3 class="event-card-name">{{ $event->event_name }}</h3>
            <div class="event-card-price">
              @if($event->offer_price)
                <del>Rs {{ number_format($event->price, 2) }}</del>
                Rs {{ number_format($event->offer_price, 2) }}
              @else
                Rs {{ number_format($event->price, 2) }}
              @endif
            </div>
            <p class="event-card-desc">{{ $desc }}</p>
            @if($event->event_date)
              <p class="event-card-desc"><i class="fas fa-calendar"></i> {{ \Illuminate\Support\Carbon::parse($event->event_date)->format('d M Y') }}</p>
            @endif
            <div class="event-card-actions">
              <button class="btn-sm btn-sm-outline" onclick="openDetail(this)" type="button" data-title="{{ e($event->event_name) }}" data-tag="{{ e($event->event_type) }}" data-price="Rs {{ number_format($price, 2) }}" data-desc="{{ e($desc) }}">View Details</button>
              <a class="btn-sm btn-sm-fill" href="https://wa.me/94766975438?text={{ $message }}" target="_blank" rel="noopener">Book Now</a>
            </div>
          </div>
        </div>
      @empty
        @foreach([
          ['Birthday Party Events','Celebrations','Rs 3,500','Magical birthday setups from kids themes to milestone adult parties.','linear-gradient(135deg,#f5a623,#ff6b6b)','fa-cake-candles'],
          ['Wedding Events','Weddings','Rs 25,000','Elegant wedding setups that transform any venue into a romantic setting.','linear-gradient(135deg,#c9a227,#e8d5a3)','fa-ring'],
          ['Engagement Events','Milestones','Rs 15,000','Beautiful engagement setups to mark your journey together.','linear-gradient(135deg,#e91e8c,#ff9a9e)','fa-gem'],
          ['Corporate Events','Business','Rs 30,000','Professional corporate event design for launches, meetings, and awards.','linear-gradient(135deg,#2c3e50,#3498db)','fa-briefcase'],
        ] as $sample)
          <div class="event-card reveal visible" data-name="{{ strtolower($sample[0] . ' ' . $sample[1] . ' ' . $sample[3]) }}">
            <div class="event-card-img" style="background:{{ $sample[4] }}"><span><i class="fas {{ $sample[5] }}"></i></span></div>
            <div class="event-card-body">
              <span class="event-card-tag">{{ $sample[1] }}</span>
              <h3 class="event-card-name">{{ $sample[0] }}</h3>
              <div class="event-card-price">{{ $sample[2] }}</div>
              <p class="event-card-desc">{{ $sample[3] }}</p>
              <div class="event-card-actions">
                <button class="btn-sm btn-sm-outline" onclick="openDetail(this)" type="button" data-title="{{ $sample[0] }}" data-tag="{{ $sample[1] }}" data-price="{{ $sample[2] }}" data-desc="{{ $sample[3] }}">View Details</button>
                <button class="btn-sm btn-sm-fill" onclick="document.getElementById('inquiry').scrollIntoView({behavior:'smooth'})" type="button">Book Now</button>
              </div>
            </div>
          </div>
        @endforeach
      @endforelse
    </div>
    <p class="no-results" id="noResults" style="display:none">No event categories match your search. Try another keyword.</p>
  </div>
</section>

<section class="why-section" id="why">
  <div class="container">
    <div class="section-header light"><span class="section-eyebrow">Our Promise</span><h2 class="section-title">Why Choose HoneyBee?</h2></div>
    <div class="features-grid">
      <div class="feature-card"><div class="feature-icon"><i class="fas fa-calendar-check"></i></div><h3>Professional Planning</h3><p>End-to-end event planning handled by our team.</p></div>
      <div class="feature-card"><div class="feature-icon"><i class="fas fa-palette"></i></div><h3>Creative Decorations</h3><p>Decor themes tailored to your vision and budget.</p></div>
      <div class="feature-card"><div class="feature-icon"><i class="fas fa-star"></i></div><h3>Quality Service</h3><p>Careful setup and support for every event.</p></div>
      <div class="feature-card"><div class="feature-icon"><i class="fas fa-clock"></i></div><h3>On-Time Delivery</h3><p>Setup completed before your guests arrive.</p></div>
    </div>
  </div>
</section>

<section class="gallery-section" id="gallery">
  <div class="container">
    <div class="section-header"><span class="section-eyebrow">Our Work</span><h2 class="section-title">Gallery</h2><p class="section-desc">A glimpse into the moments we create across Jaffna.</p></div>
    <div class="gallery-grid" id="galleryGrid">
      @foreach(['Wedding Moments','Birthday Magic','Baby Shower Bliss','Corporate Elegance','Graduation Pride','Cultural Festivities','Outdoor Events','Religious Ceremonies'] as $item)
        <div class="gallery-item"><div class="gallery-overlay"><span>{{ $item }}</span></div></div>
      @endforeach
    </div>
  </div>
</section>

<section class="inquiry-section" id="inquiry">
  <div class="container">
    <div class="inquiry-grid">
      <div class="inquiry-info">
        <span class="section-eyebrow">Get In Touch</span>
        <h2 class="section-title">Book Your Dream Event</h2>
        <p>Ready to make memories? Reach out and our team will get back to you as soon as possible.</p>
        <div class="inquiry-actions"><button class="btn-primary" onclick="document.getElementById('inquiryForm').scrollIntoView({behavior:'smooth'})" type="button"><i class="fas fa-calendar-plus"></i> Book Event</button><a href="https://wa.me/94766975438?text=Hello%20HoneyBee%20Events!%20I%27d%20like%20to%20book%20an%20event." target="_blank" rel="noopener" class="btn-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp Us</a></div>
      </div>
      <form class="inquiry-form" id="inquiryForm" onsubmit="submitInquiry(event)">
        <h3>Send an Inquiry</h3>
        <div class="form-row"><div class="form-group"><label>Your Name</label><input type="text" required></div><div class="form-group"><label>Phone Number</label><input type="tel" required></div></div>
        <div class="form-group"><label>Email Address</label><input type="email"></div>
        <div class="form-row"><div class="form-group"><label>Event Type</label><select required><option value="">Select category</option>@foreach($events as $event)<option>{{ $event->event_name }}</option>@endforeach<option>Birthday Party</option><option>Wedding</option><option>Corporate Event</option><option>Customized Event</option></select></div><div class="form-group"><label>Event Date</label><input type="date" required></div></div>
        <div class="form-group"><label>Message</label><textarea rows="4" placeholder="Tell us about your vision..."></textarea></div>
        <button type="submit" class="btn-primary full-width">Send Inquiry <i class="fas fa-paper-plane"></i></button>
      </form>
    </div>
  </div>
</section>

<section class="contact-section" id="contact">
  <div class="container">
    <div class="contact-grid">
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-phone-alt"></i></div><h4>Phone</h4><p>+94 0766975438</p></div>
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-envelope"></i></div><h4>Email</h4><p>Honeybeeevents99@gmail.com</p></div>
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div><h4>Address</h4><p>190, Sir. Pom Ramanathan Road,<br>Thirunelvely, Jaffna</p></div>
      <div class="contact-item"><div class="contact-icon"><i class="fas fa-share-alt"></i></div><h4>Follow Us</h4><div class="social-links d-flex flex-wrap justify-content-center gap-2"><a href="#" class="btn btn-outline-warning rounded-circle social-link social-facebook"><i class="fab fa-facebook-f"></i></a><a href="#" class="btn btn-outline-warning rounded-circle social-link social-instagram"><i class="fab fa-instagram"></i></a><a href="https://wa.me/94766975438" target="_blank" rel="noopener" class="btn btn-outline-warning rounded-circle social-link social-whatsapp"><i class="fab fa-whatsapp"></i></a></div></div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand"><img src="{{ asset('images/logo.png') }}" alt="HoneyBee" class="footer-logo"><p>Creating unforgettable event experiences across Jaffna District.</p></div>
      <div class="footer-links"><h5>Quick Links</h5><a href="#home">Home</a><a href="#events">Events</a><a href="#gallery">Gallery</a><a href="#inquiry">Book Event</a><a href="#contact">Contact</a></div>
      <div class="footer-links"><h5>Event Types</h5><a href="#events">Weddings</a><a href="#events">Birthdays</a><a href="#events">Corporate</a><a href="#events">Religious</a></div>
      <div class="footer-contact"><h5>Contact Info</h5><ul style="list-style:none;padding:0;"><li><i class="fas fa-phone-alt"></i> +94 0766975438</li><li><i class="fas fa-envelope"></i> Honeybeeevents99@gmail.com</li><li><i class="fas fa-map-marker-alt"></i> Jaffna, Sri Lanka</li></ul></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2025 HoneyBee Gifts &amp; Designs. All rights reserved.</p></div>
  </div>
</footer>

<div class="modal-overlay" id="detailOverlay" onclick="closeDetailIfOutside(event)">
  <div class="detail-modal">
    <button class="admin-close" onclick="closeDetail()" type="button"><i class="fas fa-times"></i></button>
    <div class="detail-img-wrap"><div class="detail-img-placeholder" id="detailImgIcon"><span><i class="fas fa-calendar-check"></i></span></div></div>
    <div class="detail-body">
      <span class="detail-tag" id="detailTag"></span>
      <h2 id="detailName"></h2>
      <p class="detail-price" id="detailPrice"></p>
      <p id="detailDesc"></p>
      <div class="detail-actions"><a href="https://wa.me/94766975438" target="_blank" rel="noopener" class="btn-whatsapp"><i class="fab fa-whatsapp"></i> Book via WhatsApp</a><button class="btn-primary" onclick="closeDetail(); document.getElementById('inquiry').scrollIntoView({behavior:'smooth'})" type="button">Book Now</button></div>
    </div>
  </div>
</div>
<div class="toast" id="toast"></div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script>
function filterEvents() {
  const query = document.getElementById('searchInput').value.trim().toLowerCase();
  const cards = document.querySelectorAll('.event-card');
  const clearBtn = document.getElementById('clearBtn');
  const hint = document.getElementById('searchHint');
  let shown = 0;
  clearBtn.style.display = query ? 'block' : 'none';
  cards.forEach(card => {
    const match = !query || (card.dataset.name || '').includes(query);
    card.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('noResults').style.display = shown ? 'none' : 'block';
  hint.textContent = query && shown ? shown + ' event' + (shown === 1 ? '' : 's') + ' found for "' + query + '"' : '';
}
function clearSearch() {
  document.getElementById('searchInput').value = '';
  filterEvents();
}
function openDetail(button) {
  document.getElementById('detailTag').textContent = button.dataset.tag;
  document.getElementById('detailName').textContent = button.dataset.title;
  document.getElementById('detailPrice').textContent = button.dataset.price;
  document.getElementById('detailDesc').textContent = button.dataset.desc;
  document.getElementById('detailOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeDetail() {
  document.getElementById('detailOverlay').classList.remove('open');
  document.body.style.overflow = '';
}
function closeDetailIfOutside(e) {
  if (e.target === document.getElementById('detailOverlay')) closeDetail();
}
function submitInquiry(e) {
  e.preventDefault();
  showToast('Inquiry sent. We will contact you soon.');
  e.target.reset();
}
function showToast(message) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3200);
}
const header = document.getElementById('header');
const hamburger = document.getElementById('hamburger');
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => header.classList.toggle('scrolled', window.scrollY > 40));
if (hamburger && nav) {
  hamburger.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('open');
    hamburger.classList.toggle('open', isOpen);
    hamburger.setAttribute('aria-expanded', String(isOpen));
  });
}
</script>
</body>
</html>
