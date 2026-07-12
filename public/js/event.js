/* ============================================================
   HoneyBee Gifts & Designs — Events Webpage Script
   ============================================================ */

/* ============================================================
   EVENT DATA
   ============================================================ */
const eventCategories = [
  {
    name: "Birthday Party Events",
    tag: "Celebrations",
    price: "Rs 3,500",
    emoji: "🎂",
    bgGradient: "linear-gradient(135deg,#f5a623,#ff6b6b)",
    desc: "Magical birthday setups from kids' themes to milestone adult parties — balloon arches, custom cakes & photo zones.",
    features: ["Custom balloon arches & décor", "Themed photo booths", "Cake table styling", "Personalized banners"]
  },
  {
    name: "Wedding Events",
    tag: "Weddings",
    price: "Rs 25,000",
    emoji: "💍",
    bgGradient: "linear-gradient(135deg,#c9a227,#e8d5a3)",
    desc: "Elegant, timeless wedding setups that transform any venue into a romantic dream — from intimate to grand ceremonies.",
    features: ["Floral mandap / altar design", "Stage & seating décor", "Bridal entrance styling", "Lighting design"]
  },
  {
    name: "Engagement Events",
    tag: "Milestones",
    price: "Rs 15,000",
    emoji: "💎",
    bgGradient: "linear-gradient(135deg,#e91e8c,#ff9a9e)",
    desc: "Beautiful engagement setups to mark your journey together — intimate, elegant and full of personality.",
    features: ["Ring ceremony stage", "Floral arrangements", "Photo wall backdrops", "Candle light setups"]
  },
  {
    name: "Anniversary Events",
    tag: "Celebrations",
    price: "Rs 8,500",
    emoji: "❤️",
    bgGradient: "linear-gradient(135deg,#e74c3c,#c0392b)",
    desc: "Celebrate years of love with a bespoke anniversary setup — romantic, tasteful and tailored to your story.",
    features: ["Romantic table setting", "Memory photo displays", "Candle & floral décor", "Personalized messages"]
  },
  {
    name: "Baby Shower Events",
    tag: "Family",
    price: "Rs 10,000",
    emoji: "🍼",
    bgGradient: "linear-gradient(135deg,#a8edea,#fed6e3)",
    desc: "Sweet, dreamy setups to welcome the little one — gender reveal elements, pastel themes and playful decorations.",
    features: ["Gender reveal décor", "Pastel theme setups", "Baby cake table", "Mama-to-be chair decoration"]
  },
  {
    name: "Corporate Events",
    tag: "Business",
    price: "Rs 30,000",
    emoji: "🏢",
    bgGradient: "linear-gradient(135deg,#2c3e50,#3498db)",
    desc: "Professional, polished corporate event design — product launches, team milestones, AGMs and brand activations.",
    features: ["Branded stage & backdrop", "Conference hall styling", "Award ceremony setup", "Photo opportunity zones"]
  },
  {
    name: "School Events",
    tag: "Education",
    price: "Rs 12,000",
    emoji: "🎓",
    bgGradient: "linear-gradient(135deg,#667eea,#764ba2)",
    desc: "Fun and vibrant setups for school competitions, prize-givings, sports days and farewell events.",
    features: ["Prize-giving stage décor", "Colour day setups", "Farewell event styling", "Sports day banners"]
  },
  {
    name: "Cultural Events",
    tag: "Culture",
    price: "Rs 18,000",
    emoji: "🪔",
    bgGradient: "linear-gradient(135deg,#f5a623,#e85d04)",
    desc: "Authentic cultural event setups with traditional elements — Thai Pongal, Deepavali, Sinhala New Year and more.",
    features: ["Traditional décor elements", "Cultural stage design", "Kolam & floral art", "Traditional lighting"]
  },
  {
    name: "Religious Events",
    tag: "Religious",
    price: "Rs 7,500",
    emoji: "🕌",
    bgGradient: "linear-gradient(135deg,#1a6e3f,#52c234)",
    desc: "Respectful, dignified setups for religious ceremonies — Pooja rooms, church events, mosque functions and more.",
    features: ["Pooja / altar décor", "Flower arrangements", "Lighting setup", "Sacred space styling"]
  },
  {
    name: "Family Gatherings",
    tag: "Family",
    price: "Rs 9,000",
    emoji: "👨‍👩‍👧‍👦",
    bgGradient: "linear-gradient(135deg,#f093fb,#f5576c)",
    desc: "Warm, welcoming setups for family reunions, homecoming parties and festive get-togethers.",
    features: ["Outdoor canopy setup", "Family photo wall", "Buffet table styling", "Garden party décor"]
  },
  {
    name: "Outdoor Events",
    tag: "Outdoor",
    price: "Rs 20,000",
    emoji: "🌿",
    bgGradient: "linear-gradient(135deg,#43e97b,#38f9d7)",
    desc: "Stunning open-air setups — garden parties, beach events, farm gatherings and outdoor receptions.",
    features: ["Tent & canopy design", "Fairy light installations", "Natural foliage décor", "Outdoor seating styling"]
  },
  {
    name: "Graduation Events",
    tag: "Milestones",
    price: "Rs 14,000",
    emoji: "🎓",
    bgGradient: "linear-gradient(135deg,#4facfe,#00f2fe)",
    desc: "Celebrate academic achievements in style — stage setups, photo areas and receptions for grads and their families.",
    features: ["Graduation stage décor", "Cap & gown photo zone", "Reception table setup", "Personalised ceremony banner"]
  },
  {
    name: "Customized Events",
    tag: "Custom",
    price: "Price on request",
    emoji: "✨",
    bgGradient: "linear-gradient(135deg,#f5a623,#ffd166)",
    desc: "Have a unique vision? We bring any concept to life — fully bespoke event design tailored exactly to your brief.",
    features: ["Concept development", "Unique theme execution", "Full setup & breakdown", "Dedicated design consultation"]
  }
];

function renderEvents(data) {
  const grid = document.getElementById('eventsGrid');
  grid.innerHTML = '';

  if (data.length === 0) {
    document.getElementById('noResults').style.display = 'block';
    return;
  }
  document.getElementById('noResults').style.display = 'none';

  data.forEach((ev, i) => {
    const originalIndex = eventCategories.indexOf(ev);
    const card = document.createElement('div');
    card.className = 'event-card reveal';
    card.innerHTML = `
      <div class="event-card-img" style="background:${ev.bgGradient}">
        <span style="position:relative;z-index:1;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.25))">${ev.emoji}</span>
      </div>
      <div class="event-card-body">
        <span class="event-card-tag">${ev.tag}</span>
        <h3 class="event-card-name">${ev.name}</h3>
        <div class="event-card-price">${ev.price}</div>
        <p class="event-card-desc">${ev.desc}</p>
        <div class="event-card-actions">
          <button class="btn-sm btn-sm-outline" onclick="openDetail(${originalIndex})">View Details</button>
          <button class="btn-sm btn-sm-fill" onclick="bookNow('${ev.name}')">Book Now</button>
        </div>
      </div>
    `;
    grid.appendChild(card);

    setTimeout(() => {
      card.classList.add('visible');
    }, i * 60);
  });
}

function filterEvents() {
  const query = document.getElementById('searchInput').value.trim().toLowerCase();
  const clearBtn = document.getElementById('clearBtn');
  const hint = document.getElementById('searchHint');

  clearBtn.style.display = query ? 'block' : 'none';

  if (!query) {
    renderEvents(eventCategories);
    hint.textContent = '';
    return;
  }

  const filtered = eventCategories.filter(ev =>
    ev.name.toLowerCase().includes(query) ||
    ev.tag.toLowerCase().includes(query) ||
    ev.desc.toLowerCase().includes(query)
  );

  renderEvents(filtered);
  hint.textContent = filtered.length
    ? `${filtered.length} categor${filtered.length === 1 ? 'y' : 'ies'} found for "${query}"`
    : '';
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  document.getElementById('clearBtn').style.display = 'none';
  document.getElementById('searchHint').textContent = '';
  renderEvents(eventCategories);
}

function openDetail(index) {
  const ev = eventCategories[index];
  const overlay = document.getElementById('detailOverlay');

  document.getElementById('detailImgIcon').innerHTML = `<span style="background:${ev.bgGradient};width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:5rem;border-radius:0">${ev.emoji}</span>`;
  document.getElementById('detailImgIcon').style.width = '100%';
  document.getElementById('detailImgIcon').style.height = '100%';
  document.getElementById('detailImgIcon').style.display = 'flex';
  document.getElementById('detailTag').textContent = ev.tag;
  document.getElementById('detailName').textContent = ev.name;
  document.getElementById('detailPrice').textContent = ev.price;
  document.getElementById('detailDesc').textContent = ev.desc;

  const featuresList = document.getElementById('detailFeatures');
  featuresList.innerHTML = ev.features.map(f => `<li>${f}</li>`).join('');

  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetail() {
  document.getElementById('detailOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

function closeDetailIfOutside(e) {
  if (e.target === document.getElementById('detailOverlay')) closeDetail();
}

function bookNow(name) {
  document.getElementById('inquiry').scrollIntoView({ behavior: 'smooth' });
  setTimeout(() => {
    const sel = document.querySelector('#inquiryForm select');
    if (sel) {
      const cleanName = name.replace(' Events', '').trim();
      for (let opt of sel.options) {
        if (opt.text.toLowerCase().includes(cleanName.toLowerCase())) {
          sel.value = opt.value;
          break;
        }
      }
    }
  }, 600);
  showToast(`📅 Booking form ready for ${name}`);
}

function toggleCart() {
  showToast('Your cart is empty. Choose an event package to get started.');
}

function openAdmin() {
  const adminOverlay = document.getElementById('adminOverlay');
  if (!adminOverlay) {
    showToast('Admin panel is not available on this page.');
    return;
  }
  adminOverlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeAdmin() {
  const adminOverlay = document.getElementById('adminOverlay');
  if (!adminOverlay) return;
  adminOverlay.classList.remove('open');
  document.body.style.overflow = '';
}

function closeAdminIfOutside(e) {
  const adminOverlay = document.getElementById('adminOverlay');
  if (adminOverlay && e.target === adminOverlay) closeAdmin();
}

function showAdminTab(tabId, btn) {
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.admin-nav-btn').forEach(b => b.classList.remove('active'));

  const tab = document.getElementById('tab-' + tabId);
  if (!tab) return;
  tab.classList.add('active');
  btn.classList.add('active');
}

function adminAlert(msg) {
  showToast('✅ ' + msg);
}

function submitInquiry(e) {
  e.preventDefault();
  showToast('🎉 Inquiry sent! We\'ll be in touch within 24 hours.');
  e.target.reset();
}

function showToast(msg) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}

const header = document.getElementById('header');

window.addEventListener('scroll', () => {
  if (window.scrollY > 40) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }

  const sections = ['home','events','gallery','contact'];
  let current = '';
  sections.forEach(id => {
    const sec = document.getElementById(id);
    if (sec && window.scrollY >= sec.offsetTop - 100) current = id;
  });
  document.querySelectorAll('.nav-link').forEach(link => {
    link.classList.toggle('active', link.getAttribute('href') === '#' + current);
  });
});

const hamburger = document.getElementById('hamburger');
const nav = document.getElementById('nav');

if (hamburger && nav) {
  hamburger.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('open');
    hamburger.classList.toggle('open', isOpen);
    hamburger.setAttribute('aria-expanded', String(isOpen));
  });
}

document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    if (!nav || !hamburger) return;
    nav.classList.remove('open');
    hamburger.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
  });
});

function initReveal() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

function watchReveal() {
  const mutObs = new MutationObserver(() => {
    document.querySelectorAll('.reveal:not(.observed)').forEach(el => {
      el.classList.add('observed');
      const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
          if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
        });
      }, { threshold: 0.1 });
      io.observe(el);
    });
  });
  const eventsGrid = document.getElementById('eventsGrid');
  if (eventsGrid) mutObs.observe(eventsGrid, { childList: true });
}

document.addEventListener('DOMContentLoaded', () => {
  renderEvents(eventCategories);
  initReveal();
  watchReveal();
});
