/* ════════════════════════════════════════════
   HONEYBEE GIFTS & DESIGNS — script.js
   ════════════════════════════════════════════ */

'use strict';

const WHATSAPP_NUMBER = '94767158873';

// ── Product data (mirrors the DOM for search) ──
const PRODUCTS = [
  { name: 'Birthday Gifts',    category: 'Gifts',  price: 1500,  desc: 'Vibrant, personalised boxes, cake toppers, and custom hampers to celebrate every age.' },
  { name: 'Wedding Gifts',     category: 'Gifts',  price: 3500,  desc: 'Elegant keepsake sets, engraved trays, and personalised couple frames.' },
  { name: 'Anniversary Gifts', category: 'Gifts',  price: 2800,  desc: 'Timeless romance — custom photo books, heart frames, and memory boxes.' },
  { name: 'Customized Gifts',  category: 'Gifts',  price: 1200,  desc: 'Upload your design — we print it on mugs, cushions, keychains, and more.' },
  { name: 'Baby Gifts',        category: 'Gifts',  price: 2200,  desc: 'Sweet milestone sets — name frames, birth stats prints, and nursery décor.' },
  { name: 'Friendship Gifts',  category: 'Gifts',  price: 1000,  desc: 'Matching BFF sets, friendship jars, and personalised photo collages.' },
  { name: 'Photo Frames',      category: 'Frames', price: 800,   desc: 'Classic and modern photo frames in multiple sizes — 4×6 to poster size.' },
  { name: 'Wedding Frames',    category: 'Frames', price: 2500,  desc: 'Ornate gold-finish frames and collage boards for your big day photos.' },
  { name: 'Family Frames',     category: 'Frames', price: 1800,  desc: 'Multi-photo tree frames and gallery wall sets to celebrate your family story.' },
  { name: 'Wooden Frames',     category: 'Frames', price: 1500,  desc: 'Handcrafted solid wood frames with laser-engraved names or messages.' },
  { name: 'LED Frames',        category: 'Frames', price: 3200,  desc: 'Glowing backlit frames with warm LED borders — a living piece of art.' },
  { name: 'Customized Frames', category: 'Frames', price: 2000,  desc: 'Bring your vision — any size, any finish, any text, any shape. We build it.' },
];

// ── Cart state ──
let cart = JSON.parse(localStorage.getItem('hb_cart') || '[]');

// ─────────────────────────────────────────────
//  HEADER: scroll shadow + active nav links
// ─────────────────────────────────────────────
window.addEventListener('scroll', () => {
  const header = document.getElementById('header');
  if (window.scrollY > 10) {
    header.style.boxShadow = '0 2px 24px rgba(26,26,26,.18)';
  } else {
    header.style.boxShadow = '';
  }

  // Highlight nav link for current section
  const sections = ['home', 'gifts', 'frames', 'services', 'gallery', 'contact'];
  const scrollY = window.scrollY + 100;
  sections.forEach(id => {
    const el = document.getElementById(id);
    const link = document.querySelector(`.nav-links a[href="#${id}"]`);
    if (!el || !link) return;
    const top = el.offsetTop;
    const bottom = top + el.offsetHeight;
    if (scrollY >= top && scrollY < bottom) {
      link.style.color = 'var(--amber)';
    } else {
      link.style.color = '';
    }
  });
});

// ─────────────────────────────────────────────
//  HAMBURGER
// ─────────────────────────────────────────────
const hamburger = document.getElementById('hamburger');
const mainNav   = document.getElementById('main-nav');
hamburger?.addEventListener('click', () => {
  mainNav.classList.toggle('open');
  hamburger.textContent = mainNav.classList.contains('open') ? '✕' : '☰';
});
// Close nav on link click
document.querySelectorAll('.nav-links a').forEach(a => {
  a.addEventListener('click', () => {
    mainNav.classList.remove('open');
    hamburger.textContent = '☰';
  });
});

// ─────────────────────────────────────────────
//  SEARCH
// ─────────────────────────────────────────────
function searchProducts() {
  const query = document.getElementById('search-input').value.trim().toLowerCase();
  const box     = document.getElementById('search-results');

  if (!query) { box.classList.add('hidden'); box.innerHTML = ''; return; }

  const matches = PRODUCTS.filter(p => p.name.toLowerCase().includes(query) || p.category.toLowerCase().includes(query));

  if (matches.length === 0) {
    box.innerHTML = '<div class="search-result-item">No products found.</div>';
    box.classList.remove('hidden');
    return;
  }

  box.innerHTML = matches.map(p => `
    <div class="search-result-item" onclick="jumpToProduct('${p.name}')">
      <strong>${p.name}</strong> <span style="color:var(--amber);margin-left:.5rem">${p.category}</span>
      <span style="float:right;color:var(--slate)">Rs ${p.price.toLocaleString()}</span>
    </div>
  `).join('');
  box.classList.remove('hidden');
}

function jumpToProduct(name) {
  // Find the card in DOM and scroll to it
  const cards = document.querySelectorAll('.product-card');
  for (const card of cards) {
    if (card.dataset.name === name) {
      document.getElementById('search-results').classList.add('hidden');
      document.getElementById('search-input').value = '';
      card.scrollIntoView({ behavior: 'smooth', block: 'center' });
      // Flash highlight
      card.style.outline = '3px solid var(--amber)';
      card.style.outlineOffset = '4px';
      setTimeout(() => { card.style.outline = ''; card.style.outlineOffset = ''; }, 2000);
      return;
    }
  }
}

// Close search results when clicking outside
document.addEventListener('click', e => {
  const wrap = document.querySelector('.search-wrap');
  if (!wrap?.contains(e.target)) {
    document.getElementById('search-results')?.classList.add('hidden');
  }
});


// ─────────────────────────────────────────────
//  CART
// ─────────────────────────────────────────────
function saveCart() {
  localStorage.setItem('hb_cart', JSON.stringify(cart));
  updateCartCount();
  updateAdminStats();
}

function updateCartCount() {
    const total = cart.reduce((s, i) => s + i.qty, 0);

    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = total;
    }
}

function addToCart(name, price) {
  const existing = cart.find(i => i.name === name);
  if (existing) {
    existing.qty++;
  } else {
    cart.push({ name, price, qty: 1 });
  }
  saveCart();
  renderCart();
  showToast(`${name} added to cart 🛒`);
}

function removeFromCart(name) {
  cart = cart.filter(i => i.name !== name);
  saveCart();
  renderCart();
}

function renderCart() {
  const el = document.getElementById('cart-items');
  if (!el) return;
  if (cart.length === 0) {
    el.innerHTML = '<p class="cart-empty">Your cart is empty.</p>';
    document.getElementById('cart-total').textContent = 'Rs 0';
    return;
  }
  el.innerHTML = cart.map(item => `
    <div class="cart-item">
      <div>
        <div class="cart-item-name">${item.name} × ${item.qty}</div>
      </div>
      <div style="display:flex;align-items:center;gap:.75rem">
        <span class="cart-item-price">Rs ${(item.price * item.qty).toLocaleString()}</span>
        <button class="cart-item-remove" onclick="removeFromCart('${item.name}')">✕</button>
      </div>
    </div>
  `).join('');
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  document.getElementById('cart-total').textContent = `Rs ${total.toLocaleString()}`;
}

function toggleCart() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');

    if (!sidebar || !overlay) return;

    const isOpen = sidebar.classList.contains('open');

    sidebar.classList.toggle('open');
    overlay.classList.toggle('visible');

    if (!isOpen) {
        renderCart();
    }

    document.body.style.overflow = isOpen ? '' : 'hidden';
}

function checkoutWhatsapp() {
  if (cart.length === 0) { showToast('Cart is empty!'); return; }
  const lines = cart.map(i => `• ${i.name} × ${i.qty} = Rs ${(i.price * i.qty).toLocaleString()}`).join('%0A');
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const msg = `Hello HoneyBee Gifts & Designs! 🛍%0A%0AI'd like to order:%0A${lines}%0A%0ATotal: Rs ${total.toLocaleString()}%0APlease confirm availability and delivery details. Thank you!`;
  window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${msg}`, '_blank', 'noopener');
}

// ─────────────────────────────────────────────
//  WHATSAPP ORDER
// ─────────────────────────────────────────────
function whatsappOrder(productName) {
  const msg = `Hello HoneyBee Gifts & Designs! 🎁%0A%0AI'm interested in: *${productName}*%0A%0ACould you please share more details, pricing, and availability?%0A%0AThank you!`;
  window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${msg}`, '_blank', 'noopener');
}

// ─────────────────────────────────────────────
//  VIEW DETAILS
// ─────────────────────────────────────────────
function viewDetails(name) {
  const product = PRODUCTS.find(p => p.name === name);
  if (!product) return;

  document.getElementById('detail-title').textContent = product.name;
  document.getElementById('detail-body').textContent  = `${product.desc} — Starting from Rs ${product.price.toLocaleString()}.`;
  document.getElementById('detail-wa').onclick = () => whatsappOrder(name);

  document.getElementById('detail-modal').classList.add('visible');
  document.getElementById('detail-overlay').classList.add('visible');
  document.body.style.overflow = 'hidden';
}

function closeDetail() {
  document.getElementById('detail-modal').classList.remove('visible');
  document.getElementById('detail-overlay').classList.remove('visible');
  document.body.style.overflow = '';
}


// ─────────────────────────────────────────────
//  ADMIN PANEL
// ─────────────────────────────────────────────
function openAdmin() {
  document.getElementById('admin-panel').classList.add('open');
  document.getElementById('admin-overlay').classList.add('visible');
  document.body.style.overflow = 'hidden';
  updateAdminStats();
}

function closeAdmin() {
  document.getElementById('admin-panel').classList.remove('open');
  document.getElementById('admin-overlay').classList.remove('visible');
  document.body.style.overflow = '';
}

function showTab(tabId, btn) {
  // Hide all tab panes
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
  document.getElementById(`tab-${tabId}`)?.classList.add('active');
  btn.classList.add('active');
}

function updateAdminStats() {
  const cartTotal = cart.reduce((s, i) => s + i.qty, 0);
  const el = document.getElementById('stat-cart');
  if (el) el.textContent = cartTotal;
}

// ─────────────────────────────────────────────
//  CONTACT FORM
// ─────────────────────────────────────────────
function submitForm(e) {
  e.preventDefault();
  showToast("✅ Enquiry sent! We'll reach out soon.");
  e.target.reset();
}

// ─────────────────────────────────────────────
//  TOAST
// ─────────────────────────────────────────────
let toastTimer;

function showToast(msg) {
    const el = document.getElementById('toast');

    if (!el) {
        console.log(msg);
        return;
    }

    el.textContent = msg;
    el.classList.add('show');

    clearTimeout(toastTimer);

    toastTimer = setTimeout(() => {
        el.classList.remove('show');
    }, 3000);
}

// ─────────────────────────────────────────────
//  SCROLL-IN ANIMATIONS
// ─────────────────────────────────────────────
function initScrollReveal() {
  const targets = document.querySelectorAll('.product-card, .service-card, .gallery-item');
  targets.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(28px)';
    el.style.transition = 'opacity .55s ease, transform .55s ease';
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        // Stagger using a data attribute
        const delay = parseInt(entry.target.dataset.delay || 0);
        setTimeout(() => {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }, delay);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  // Add stagger delays
  document.querySelectorAll('.products-grid').forEach(grid => {
    grid.querySelectorAll('.product-card').forEach((card, idx) => {
      card.dataset.delay = idx * 80;
    });
  });
  document.querySelectorAll('.services-grid .service-card').forEach((card, idx) => {
    card.dataset.delay = idx * 70;
  });
  document.querySelectorAll('.gallery-grid .gallery-item').forEach((card, idx) => {
    card.dataset.delay = idx * 60;
  });

  targets.forEach(el => observer.observe(el));
}

// ─────────────────────────────────────────────
//  INIT
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  initScrollReveal();

  // Respect prefers-reduced-motion for scroll animations
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.product-card, .service-card, .gallery-item').forEach(el => {
      el.style.opacity = '1';
      el.style.transform = '';
    });
  }
});
