const CART_STORAGE_KEY = 'honeybee-gift-design-cart';

function loadCart() {
  try {
    const storedCart = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || '[]');
    return Array.isArray(storedCart)
      ? storedCart.filter(item => item && typeof item.name === 'string' && Number.isFinite(Number(item.price)))
      : [];
  } catch (error) {
    return [];
  }
}

const cart = loadCart();

function saveCart() {
  localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
}

function showToast(message, duration = 3000) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), duration);
}

function formatPrice(price) {
  return 'Rs ' + Number(price || 0).toLocaleString();
}

function updateCart() {
  const count = document.getElementById('cart-count');
  const total = document.getElementById('cart-total');
  const items = document.getElementById('cart-items');
  const sum = cart.reduce((acc, item) => acc + item.price, 0);

  if (count) count.textContent = cart.length;
  if (total) total.textContent = formatPrice(sum);
  if (!items) return;

  if (!cart.length) {
    items.innerHTML = '<p class="cart-empty">Your cart is empty.</p>';
    return;
  }

  items.innerHTML = cart.map((item, index) => `
    <div class="cart-item">
      <div>
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">${formatPrice(item.price)}</div>
      </div>
      <button class="cart-item-remove" onclick="removeFromCart(${index})" type="button">Remove</button>
    </div>
  `).join('');
}

function addToCart(name, price, itemId, itemType) {
  cart.push({
    name: name,
    price: Number(price || 0),
    item_id: itemId,
    item_type: itemType,
  });
  saveCart();
  updateCart();
  showToast(name + ' added to cart.');
}

function removeFromCart(index) {
  cart.splice(index, 1);
  saveCart();
  updateCart();
}

function toggleCart() {
  document.getElementById('cart-sidebar')?.classList.toggle('open');
  document.getElementById('cart-overlay')?.classList.toggle('visible');
}

function proceedToCheckout() {
  if (!cart.length) {
    showToast('Your cart is empty.');
    return;
  }

  window.location.href = window.honeyBeeCheckoutUrl;
}

function whatsappOrder(name) {
  const text = encodeURIComponent('Hello HoneyBee Gift and Design, I am interested in ' + name + '.');
  window.open('https://wa.me/94767158873?text=' + text, '_blank');
}

function requestCustomFrame() {
  const product = document.getElementById('inquiry-product');
  if (product) product.value = 'Custom Frame Work';
  document.getElementById('inquiry')?.scrollIntoView({ behavior: 'smooth' });
}

function viewDetails(name) {
  const modal = document.getElementById('detail-modal');
  const overlay = document.getElementById('detail-overlay');
  const title = document.getElementById('detail-title');
  const body = document.getElementById('detail-body');
  const button = document.getElementById('detail-wa');

  if (title) title.textContent = name;
  if (body) body.textContent = 'Custom made with your preferred colors, text, photos, size, and material. Contact us to confirm the final design and price.';
  if (button) button.onclick = () => whatsappOrder(name);

  overlay?.classList.add('visible');
  modal?.classList.add('open');
}

function closeDetail() {
  document.getElementById('detail-overlay')?.classList.remove('visible');
  document.getElementById('detail-modal')?.classList.remove('open');
}

function searchProducts() {
  const input = document.getElementById('search-input');
  const results = document.getElementById('search-results');
  const query = (input?.value || '').trim().toLowerCase();
  const cards = Array.from(document.querySelectorAll('.product-card'));
  let shown = 0;

  cards.forEach(card => {
    const text = ((card.dataset.name || '') + ' ' + (card.dataset.category || '')).toLowerCase();
    const match = !query || text.includes(query);
    card.style.display = match ? '' : 'none';
    if (match) shown++;
  });

  if (!results) return;
  if (!query) {
    results.classList.add('hidden');
    results.innerHTML = '';
    return;
  }

  results.classList.remove('hidden');
  results.innerHTML = shown
    ? `<div class="search-result-item">${shown} item${shown === 1 ? '' : 's'} found</div>`
    : '<div class="search-result-item">No matching items found.</div>';
}

function submitForm(event) {
  event.preventDefault();
  showToast('Enquiry sent. We will contact you soon.');
  event.target.reset();
}

document.addEventListener('DOMContentLoaded', () => {
  updateCart();

  const header = document.getElementById('header');
  window.addEventListener('scroll', () => {
    header?.classList.toggle('scrolled', window.scrollY > 40);
  }, { passive: true });

  const hamburger = document.getElementById('hamburger');
  const nav = document.getElementById('main-nav');
  hamburger?.addEventListener('click', () => {
    nav?.classList.toggle('open');
  });
});
