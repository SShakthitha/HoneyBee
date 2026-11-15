@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<link rel="stylesheet" href="{{ request()->getBaseUrl() }}/css/checkout.css">
@endpush

@section('content')
<section class="checkout-page">
  <div class="container checkout-container">
    <div class="checkout-heading">
      <p class="checkout-eyebrow">Almost there</p>
      <h1>Checkout</h1>
      <p>Review your gifts and frames, then open WhatsApp with a message to HoneyBee.</p>
    </div>

    @if ($errors->any())
      <div class="checkout-note" role="alert">
        <strong>We could not prepare your WhatsApp request.</strong>
        <ul class="mb-0 mt-2">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div id="checkout-empty" class="checkout-empty" hidden>
      <h2>Your cart is empty</h2>
      <p>Add a gift or frame to your cart before checking out.</p>
      <a href="{{ route('gift.design') }}" class="checkout-btn checkout-btn--primary">Continue Shopping</a>
    </div>

    <div id="checkout-content" class="checkout-grid" hidden>
      <section class="checkout-card customer-card">
        <h2>Customer information</h2>
        <dl class="customer-details">
          <div><dt>Name</dt><dd>{{ $customer?->full_name ?: auth()->user()->name }}</dd></div>
          <div><dt>Email</dt><dd>{{ $customer?->email ?: auth()->user()->email }}</dd></div>
          <div><dt>Phone</dt><dd>{{ $customer?->phone ?: 'Not provided' }}</dd></div>
          @if($customer?->address)
            <div><dt>Address</dt><dd>{{ $customer->address }}</dd></div>
          @endif
        </dl>
        @if(!$customer?->phone)
          <p class="checkout-note">Please add your phone number in your profile so we can contact you about this order.</p>
        @endif
      </section>

      <section class="checkout-card order-card">
        <h2>Your order</h2>
        <div class="order-table-wrap">
          <table class="order-table">
            <thead><tr><th>Product</th><th>Qty.</th><th>Unit price</th><th>Subtotal</th></tr></thead>
            <tbody id="checkout-items"></tbody>
            <tfoot><tr><th colspan="3">Grand total</th><th id="checkout-total">Rs 0</th></tr></tfoot>
          </table>
        </div>
        <p id="checkout-price-note" class="checkout-note" hidden>These displayed prices are an estimate from your browser cart. The WhatsApp message is rebuilt using the current catalog prices stored by HoneyBee.</p>
      </section>

      <section class="checkout-card requirements-card">

          <p class="checkout-note">This opens a WhatsApp request. No order is saved and no stock is checked or reserved at this stage. HoneyBee must confirm availability and the order before payment. Bank transfer is the only payment method; opening WhatsApp does not record payment.</p>

          <label for="special-requirements">Special requirements or notes</label>

          <textarea
              id="special-requirements"
              rows="5"
              placeholder="For example: preferred colours, custom text, delivery details, or any questions."
          ></textarea>

          <div style="margin-top:20px;">

              <form
                  id="whatsapp-order-form"
                  method="POST"
                  action="{{ route('checkout.whatsapp.prepare') }}"
                  target="honeybee-whatsapp-request"
                  rel="noopener"
                  style="flex:1;"
              >
                  @csrf

                  <button
                      type="submit"
                      class="checkout-btn checkout-btn--whatsapp"
                      style="width:100%;"
                  >
                      <i class="bi bi-whatsapp me-2"></i> Order via WhatsApp
                  </button>
              </form>
          </div>
          <p class="checkout-note mt-3">The WhatsApp message uses current catalog prices. Final availability and order confirmation happen with HoneyBee.</p>

      </section>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  window.honeyBeeCheckout = {
    cartStorageKey: 'honeybee-gift-design-cart',
    shoppingUrl: @json(request()->getBaseUrl() . '/gift-design'),
    customer: @json([
      'name' => $customer?->full_name ?: auth()->user()->name,
      'phone' => $customer?->phone ?: '',
    ]),
  };
</script>
<script src="{{ request()->getBaseUrl() }}/js/checkout.js"></script>
@endpush
