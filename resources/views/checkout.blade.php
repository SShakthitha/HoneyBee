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
      <p>Review your selected gifts, add any special requirements, and send your order to us on WhatsApp.</p>
    </div>

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
      </section>

      <section class="checkout-card requirements-card">

          <label for="special-requirements">
              Special requirements or notes
          </label>

          <textarea
              id="special-requirements"
              rows="5"
              placeholder="For example: preferred colours, custom text, delivery details, or any questions."
          ></textarea>

          <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px;">

              <form
                  id="place-order-form"
                  method="POST"
                  action="{{ route('checkout.store') }}"
                  style="flex:1;"
              >
                  @csrf

                  <button
                      type="submit"
                      class="checkout-btn checkout-btn--primary"
                      style="width:100%;"
                  >
                      Place Order
                  </button>
              </form>

              <button
                  id="checkout-whatsapp"
                  class="checkout-btn checkout-btn--whatsapp"
                  type="button"
                  style="flex:1;"
              >
                  Order via WhatsApp
              </button>

          </div>

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
