(() => {
  const config = window.honeyBeeCheckout;
  const notesStorageKey = 'honeybee-gift-design-cart-notes';

  function formatPrice(price) {
    return 'Rs ' + Number(price || 0).toLocaleString();
  }

  function readCart() {
    try {
      const cart = JSON.parse(
        localStorage.getItem(config.cartStorageKey) || '[]'
      );

      return Array.isArray(cart)
        ? cart.filter(
            item =>
              item &&
              item.name &&
              Number.isFinite(Number(item.price)) &&
              Number.isInteger(Number(item.item_id)) &&
              Number(item.item_id) > 0 &&
              ['gift', 'frame'].includes(item.item_type)
          )
        : [];
    } catch (error) {
      return [];
    }
  }

  function groupCartItems(cart) {
    return Object.values(
      cart.reduce((items, item) => {
        const price = Number(item.price);

        const key = [
          item.item_type || 'product',
            item.item_id || ''
        ].join('|');

        if (!items[key]) {
          items[key] = {
            name: item.name,
            price: price,
            quantity: 0,
            item_id: item.item_id || null,
            item_type: item.item_type || 'product'
          };
        }

        items[key].quantity += 1;

        return items;
      }, {})
    );
  }

  function showShoppingPage() {
    window.location.href = config.shoppingUrl;
  }

  // Header cart button on checkout returns to Gift & Design.
  window.toggleCart = showShoppingPage;

  document.addEventListener('DOMContentLoaded', () => {

    const cart = readCart();

    const empty = document.getElementById('checkout-empty');
    const content = document.getElementById('checkout-content');
    const cartCount = document.getElementById('cart-count');

    if (cartCount) {
      cartCount.textContent = cart.length;
    }

    if (!cart.length) {
      if (empty) {
        empty.hidden = false;
      }

      if (content) {
        content.hidden = true;
      }

      return;
    }

    if (empty) {
      empty.hidden = true;
    }

    if (content) {
      content.hidden = false;
    }

    const items = groupCartItems(cart);

    const total = items.reduce(
      (sum, item) => sum + (item.price * item.quantity),
      0
    );

    const checkoutItems = document.getElementById('checkout-items');

    if (checkoutItems) {
      checkoutItems.innerHTML = items.map(item => `
        <tr>
          <td>${escapeHtml(item.name)}</td>
          <td>${item.quantity}</td>
          <td>${formatPrice(item.price)}</td>
          <td>${formatPrice(item.price * item.quantity)}</td>
        </tr>
      `).join('');
    }

    const checkoutTotal = document.getElementById('checkout-total');

    if (checkoutTotal) {
      checkoutTotal.textContent = formatPrice(total);
    }

    const estimatedPriceNote = document.getElementById('checkout-price-note');
    if (estimatedPriceNote) {
      estimatedPriceNote.hidden = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Special requirements
    |--------------------------------------------------------------------------
    */

    const notes = document.getElementById('special-requirements');

    if (notes) {
      notes.value =
        localStorage.getItem(notesStorageKey) || '';

      notes.addEventListener('input', () => {
        localStorage.setItem(
          notesStorageKey,
          notes.value
        );
      });
    }

    /*
    |--------------------------------------------------------------------------
    | Place Order
    |--------------------------------------------------------------------------
    */

    const orderForm =
      document.getElementById('whatsapp-order-form');

    if (orderForm) {
      let submitting = false;

      orderForm.addEventListener('submit', event => {

        if (submitting) {
          event.preventDefault();
          return;
        }

        event.preventDefault();
        submitting = true;

        // Remove previously generated item fields.
        orderForm
          .querySelectorAll('.cart-generated-input')
          .forEach(input => input.remove());

        /*
        Send only product identifiers and quantities. The server resolves
        product names, categories, and current prices from the database.
        */

        items.forEach((item, index) => {

          addHiddenInput(
            orderForm,
            `items[${index}][quantity]`,
            item.quantity
          );

          if (item.item_id !== null) {
            addHiddenInput(
              orderForm,
              `items[${index}][item_id]`,
              item.item_id
            );
          }

          addHiddenInput(
            orderForm,
            `items[${index}][item_type]`,
            item.item_type
          );
        });

        addHiddenInput(
          orderForm,
          'notes',
          notes ? notes.value.trim() : ''
        );

        const button = orderForm.querySelector('button[type="submit"]');
        if (button) {
          button.disabled = true;
          button.textContent = 'Preparing WhatsApp request…';
        }

        orderForm.submit();
      });
    }

  });

  function addHiddenInput(form, name, value) {

    const input = document.createElement('input');

    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.className = 'cart-generated-input';

    form.appendChild(input);
  }

  function escapeHtml(value) {

    const element = document.createElement('div');

    element.textContent = value;

    return element.innerHTML;
  }

})();
