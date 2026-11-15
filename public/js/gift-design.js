const CART_STORAGE_KEY = 'honeybee-gift-design-cart';

function loadCart() {
  try {
    const storedCart = JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || '[]');
    return Array.isArray(storedCart)
      ? storedCart
        .filter(item => item && typeof item.name === 'string' && Number.isFinite(Number(item.price)))
        .map(item => ({ ...item, price: Number(item.price) }))
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

  items.replaceChildren(...cart.map((item, index) => {
    const row = document.createElement('div');
    row.className = 'cart-item';
    const details = document.createElement('div');
    const name = document.createElement('div');
    name.className = 'cart-item-name';
    name.textContent = item.name;
    const price = document.createElement('div');
    price.className = 'cart-item-price';
    price.textContent = formatPrice(item.price);
    details.append(name, price);
    const remove = document.createElement('button');
    remove.className = 'cart-item-remove';
    remove.type = 'button';
    remove.textContent = 'Remove';
    remove.addEventListener('click', () => removeFromCart(index));
    row.append(details, remove);
    return row;
  }));
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

function prepareWhatsAppCart(event) {
  event.preventDefault();
  const form = event.currentTarget;
  if (!cart.length) {
    showToast('Your cart is empty.');
    return;
  }

  const invalidItem = cart.some(item =>
    !Number.isInteger(Number(item.item_id)) ||
    Number(item.item_id) < 1 ||
    !['gift', 'frame'].includes(item.item_type)
  );
  if (invalidItem) {
    showToast('A cart item is invalid. Remove it and add the product again.');
    return;
  }

  form.querySelectorAll('.cart-generated-input').forEach(input => input.remove());
  const grouped = new Map();
  cart.forEach(item => {
    const key = `${item.item_type}|${Number(item.item_id)}`;
    grouped.set(key, (grouped.get(key) || 0) + 1);
  });
  Array.from(grouped.entries()).forEach(([key, quantity], index) => {
    const [type, itemId] = key.split('|');
    addCartRequestInput(form, `items[${index}][item_id]`, Number(itemId));
    addCartRequestInput(form, `items[${index}][item_type]`, type);
    addCartRequestInput(form, `items[${index}][quantity]`, quantity);
  });

  const notes = document.getElementById('cart-order-notes');
  addCartRequestInput(form, 'notes', notes ? notes.value.trim() : '');

  const button = document.getElementById('checkout-whatsapp-button');
  if (button) {
    button.disabled = true;
    button.textContent = 'Preparing WhatsApp request…';
  }

  HTMLFormElement.prototype.submit.call(form);
}

function addCartRequestInput(form, name, value) {
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = name;
  input.value = value;
  input.className = 'cart-generated-input';
  form.appendChild(input);
}

function whatsappOrder(name) {
  const text = encodeURIComponent('Hello HoneyBee Gift and Design, I am interested in ' + name + '.');
  const number = window.honeyBeeContact?.whatsappNumber;
  if (!number) return;
  window.open('https://wa.me/' + number + '?text=' + text, '_blank', 'noopener');
}

function requestCustomFrame() {
  const product = document.getElementById('inquiry-product');
  if (product) product.value = 'Custom Frame Work';
  document.getElementById('inquiry')?.scrollIntoView({ behavior: 'smooth' });
}

function viewDetails(name, description = '') {
  const modal = document.getElementById('detail-modal');
  const overlay = document.getElementById('detail-overlay');
  const title = document.getElementById('detail-title');
  const body = document.getElementById('detail-body');
  const button = document.getElementById('detail-wa');

  if (title) title.textContent = name;
  if (body) body.textContent = description || 'Contact HoneyBee to discuss this product and any customization options.';
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

document.addEventListener('click', event => {
  const button = event.target.closest('[data-cart-product], [data-detail-product]');
  if (!button) return;

  const name = button.dataset.productName || '';
  const price = Number(button.dataset.productPrice || 0);
  const id = button.dataset.productId;
  const type = button.dataset.productType;

  if (button.hasAttribute('data-cart-product')) addToCart(name, price, id, type);
  else viewDetails(name, button.dataset.productDescription || '');
});

document.addEventListener('DOMContentLoaded', () => {
  updateCart();

  document.getElementById('whatsapp-cart-form')?.addEventListener('submit', prepareWhatsAppCart);

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
