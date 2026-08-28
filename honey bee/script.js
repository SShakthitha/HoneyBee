document.getElementById('year').textContent = new Date().getFullYear();

// Mobile nav toggle
const hamburger = document.getElementById('hamburgerBtn');
const mainNav = document.getElementById('main-nav');
hamburger.addEventListener('click', () => mainNav.classList.toggle('open'));
mainNav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => mainNav.classList.remove('open')));

// Header scroll shadow
const header = document.getElementById('header');
window.addEventListener('scroll', () => {
  header.classList.toggle('scrolled', window.scrollY > 20);
});

// Price tabs
document.querySelectorAll('.price-tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.price-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.price-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.target).classList.add('active');
  });
});
