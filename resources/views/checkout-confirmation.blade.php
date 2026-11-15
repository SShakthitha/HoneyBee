@extends('layouts.app')

@section('title', 'Order Placed Successfully')

@push('styles')
<link rel="stylesheet" href="{{ request()->getBaseUrl() }}/css/checkout.css">
@endpush

@section('content')
<section class="checkout-page">
    <div class="container checkout-container">
        <div class="checkout-empty confirmation-card">
            <div class="confirmation-icon"><i class="bi bi-check-lg"></i></div>
            <p class="checkout-eyebrow">Order placed successfully</p>
            <h1>Thank you for your order!</h1>
            <p>Your order has been saved successfully. Your order ID is <strong>#{{ $order->order_id }}</strong>.</p>
            @if ($whatsAppUrl)
                <p class="confirmation-note">WhatsApp is only used to contact HoneyBee Shop about your saved order.</p>
                <a class="checkout-btn checkout-btn--whatsapp" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp me-2"></i> Continue to WhatsApp</a>
            @else
                <p class="confirmation-note">WhatsApp ordering is currently unavailable. Your order has still been placed successfully.</p>
            @endif
            <a class="confirmation-link" href="{{ route('orders.show', $order->order_id) }}">View order details</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
  localStorage.removeItem('honeybee-gift-design-cart');
  localStorage.removeItem('honeybee-gift-design-cart-notes');
</script>
@endpush
