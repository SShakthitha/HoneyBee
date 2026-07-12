/**
 * LASER WORKS — script.js
 * Interactive behaviours: sticky header, mobile nav, search,
 * product modal, admin panel, form submission, smooth scroll, toast.
 */

/* ─── DOM READY ─────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initHamburger();
  initNavHighlight();
  initScrollAnimations();
});

/* ─── STICKY HEADER ─────────────────────────────────────── */
function initHeader() {
  const header = document.getElementById('header');
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 60);
  }, { passive: true });
}

/* ─── HAMBURGER MENU ────────────────────────────────────── */
function initHamburger() {
  const btn  = document.getElementById('hamburger');
  const nav  = document.getElementById('nav');

  if (!btn || !nav) return;

  btn.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    btn.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', String(open));
  });

  // Close nav when a link is clicked
  nav.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
      btn.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    });
  });

  // Close on outside click
  document.addEventListener('click', e => {
    if (!nav.contains(e.target) && !btn.contains(e.target)) {
      nav.classList.remove('open');
      btn.classList.remove('open');
    }
  });
}

/* ─── ACTIVE NAV HIGHLIGHT ON SCROLL ───────────────────── */
function initNavHighlight() {
  const sections = document.querySelectorAll('section[id]');
  const links    = document.querySelectorAll('.nav-link[href^="#"]');

  if (!links.length) return;

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        links.forEach(l => l.classList.remove('active'));
        const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
        if (active) active.classList.add('active');
      }
    });
  }, { rootMargin: '-50% 0px -50% 0px' });

  sections.forEach(s => observer.observe(s));
}

function toggleCart() {
  showToast('Your cart is empty. Choose a laser product to get started.');
}

/* ─── SCROLL ANIMATIONS ─────────────────────────────────── */
function initScrollAnimations() {
  const targets = document.querySelectorAll(
    '.product-card, .service-card, .gallery-item, .contact-item'
  );

  targets.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(28px)';
    el.style.transition = 'opacity .5s ease, transform .5s ease';
  });

  const observer = new IntersectionObserver(entries => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }, 60 * (entry.target.dataset.delay || 0));
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  let delay = 0;
  targets.forEach((el, i) => {
    el.dataset.delay = delay++;
    if (delay > 5) delay = 0;
    observer.observe(el);
  });
}

/* ─── SEARCH ────────────────────────────────────────────── */
const productList = [
  'Laser Photo Frames',
  'Customized Name Boards',
  'Wooden Name Plates',
  'Acrylic Name Boards',
  'Customized Key Tags',
  'Laser Engraved Gifts',
  'Wedding Name Boards',
  'Birthday Decorations',
  'Wall Art Designs',
  'Corporate Laser Products',
  'Trophy Award Engraving',
  'Customized Laser Cut Designs'
];

function liveSearch(query) {
  const resultsEl = document.getElementById('searchResults');
  const q = query.trim().toLowerCase();

  if (!q) { resultsEl.innerHTML = ''; return; }

  const matches = productList.filter(p => p.toLowerCase().includes(q));

  if (!matches.length) {
    resultsEl.innerHTML = '<span class="search-no-results">No products found. Try a different term.</span>';
    return;
  }

  resultsEl.innerHTML = matches
    .map(m => `<span class="search-tag" onclick="jumpToProduct('${m}')">${m}</span>`)
    .join('');
}

function performSearch() {
  const q = document.getElementById('searchInput').value.trim().toLowerCase();
  if (!q) { showToast('Please enter a search term.'); return; }

  const cards = document.querySelectorAll('.product-card');
  let found = 0;

  cards.forEach(card => {
    const name = (card.dataset.name || '').toLowerCase();
    if (name.includes(q)) {
      card.style.display = '';
      found++;
    } else {
      card.style.display = 'none';
    }
  });

  if (found === 0) {
    showToast('No products match your search. Showing all.');
    cards.forEach(c => c.style.display = '');
  } else {
    document.getElementById('products').scrollIntoView({ behavior: 'smooth', block: 'start' });
    showToast(`Found ${found} product${found > 1 ? 's' : ''} matching "${q}"`);
  }
}

function jumpToProduct(name) {
  document.getElementById('searchInput').value = name;
  document.getElementById('searchResults').innerHTML = '';
  const cards = document.querySelectorAll('.product-card');
  cards.forEach(c => c.style.display = '');

  const match = [...cards].find(c =>
    (c.dataset.name || '').toLowerCase().includes(name.toLowerCase())
  );
  if (match) {
    match.scrollIntoView({ behavior: 'smooth', block: 'center' });
    match.style.outline = '3px solid var(--amber)';
    setTimeout(() => match.style.outline = '', 1800);
  }
}

/* ─── PRODUCT DETAIL MODAL ──────────────────────────────── */
const iconMap = {
  'Laser Photo Frames':         'fa-image',
  'Customized Name Boards':     'fa-sign-hanging',
  'Wooden Name Plates':         'fa-tree',
  'Acrylic Name Boards':        'fa-gem',
  'Customized Key Tags':        'fa-key',
  'Laser Engraved Gifts':       'fa-gift',
  'Wedding Name Boards':        'fa-heart',
  'Birthday Decorations':       'fa-cake-candles',
  'Wall Art Designs':           'fa-palette',
  'Corporate Laser Products':   'fa-briefcase',
  'Trophy & Award Engraving':   'fa-trophy',
  'Trophy Award Engraving':     'fa-trophy',
  'Customized Laser Cut Designs':'fa-drafting-compass'
};

const bgMap = {
  'Laser Photo Frames':         'linear-gradient(135deg,#3d2b1f,#7a4e2d)',
  'Customized Name Boards':     'linear-gradient(135deg,#1a2a4a,#2e4d8f)',
  'Wooden Name Plates':         'linear-gradient(135deg,#4a3000,#8b5a00)',
  'Acrylic Name Boards':        'linear-gradient(135deg,#0d3d4a,#0e7d95)',
  'Customized Key Tags':        'linear-gradient(135deg,#2d2d00,#7a7a00)',
  'Laser Engraved Gifts':       'linear-gradient(135deg,#4a1a2a,#9e3055)',
  'Wedding Name Boards':        'linear-gradient(135deg,#3a1a4a,#8040aa)',
  'Birthday Decorations':       'linear-gradient(135deg,#4a2a00,#c86400)',
  'Wall Art Designs':           'linear-gradient(135deg,#1a3a1a,#2e8b2e)',
  'Corporate Laser Products':   'linear-gradient(135deg,#1a1a3a,#303082)',
  'Trophy Award Engraving':     'linear-gradient(135deg,#3a2a00,#b8860b)',
  'Trophy & Award Engraving':   'linear-gradient(135deg,#3a2a00,#b8860b)',
  'Customized Laser Cut Designs':'linear-gradient(135deg,#1a3a3a,#1a8080)'
};

function openModal(name, desc) {
  const modal   = document.getElementById('productModal');
  const titleEl = document.getElementById('productModalTitle');
  const descEl  = document.getElementById('productModalDesc');
  const imgEl   = document.getElementById('productModalImg');

  if (!modal) return;

  titleEl.textContent = name;
  descEl.textContent  = desc || 'Precision laser-crafted product made just for you. Contact us to discuss sizes, materials, and pricing.';
  imgEl.style.background = bgMap[name] || 'linear-gradient(135deg,#1a1a1a,#4a4a4a)';
  imgEl.innerHTML = `<i class="fas ${iconMap[name] || 'fa-cube'}"></i>`;

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeProductModal() {
  const modal = document.getElementById('productModal');
  if (modal) modal.classList.remove('open');
  document.body.style.overflow = '';
}

/* ─── ORDER FORM QUICK FILL ─────────────────────────────── */
function openOrderForm(productName) {
  const select = document.getElementById('fproduct');
  if (select) {
    for (let opt of select.options) {
      if (opt.text.toLowerCase().includes(productName.toLowerCase().split(' ')[0])) {
        select.value = opt.value;
        break;
      }
    }
  }
  scrollToForm();
}

/* ─── SCROLL TO FORM ────────────────────────────────────── */
function scrollToForm() {
  const form = document.getElementById('contactForm');
  if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ─── INQUIRY FORM SUBMIT ───────────────────────────────── */
function submitInquiry() {
  const name    = document.getElementById('fname').value.trim();
  const phone   = document.getElementById('fphone').value.trim();
  const email   = document.getElementById('femail').value.trim();
  const product = document.getElementById('fproduct').value;
  const message = document.getElementById('fmessage').value.trim();

  if (!name)    { showToast('Please enter your full name.'); return; }
  if (!phone)   { showToast('Please enter your phone number.'); return; }
  if (!product) { showToast('Please select a product.'); return; }
  if (!message) { showToast('Please describe your requirements.'); return; }

  showToast('✓ Inquiry sent! We will contact you within 24 hours.');

  document.getElementById('fname').value    = '';
  document.getElementById('fphone').value   = '';
  document.getElementById('femail').value   = '';
  document.getElementById('fproduct').value = '';
  document.getElementById('fmessage').value = '';
}

/* ─── CLOSE MODALS ON OVERLAY CLICK ────────────────────── */
const modal = document.getElementById('productModal');
if (modal) {
  modal.addEventListener('click', function(e) {
    if (e.target === this) closeProductModal();
  });
}

/* ─── CLOSE ON ESC ──────────────────────────────────────── */
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    closeProductModal();
  }
});

/* ─── TOAST ─────────────────────────────────────────────── */
let toastTimer = null;
function showToast(message, duration = 3200) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  
  toast.textContent = message;
  toast.classList.add('show');

  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove('show'), duration);
}

/* ─── GALLERY ITEM LIGHTBOX ────────────────────────────── */
document.querySelectorAll('.gallery-placeholder').forEach(item => {
  item.addEventListener('click', () => {
    const label = item.querySelector('span')?.textContent || 'Gallery item';
    showToast(`📸 "${label}" — full images coming soon!`);
  });
});
