@extends('layouts.app')

@section('title', 'Continue to WhatsApp')

@push('styles')
<link rel="stylesheet" href="{{ request()->getBaseUrl() }}/css/checkout.css">
@endpush

@section('content')
<section class="checkout-page">
    <div class="container checkout-container">
        <div class="checkout-empty confirmation-card">
            <p class="checkout-eyebrow">WhatsApp request prepared</p>
            <h1>Continue to HoneyBee</h1>
            <p>Your message contains catalog prices and your selected items. This is only a request: no order has been saved, stock has not been checked or reserved, and no payment has been recorded.</p>
            <a class="checkout-btn checkout-btn--whatsapp" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-whatsapp me-2"></i> Open WhatsApp if it did not open
            </a>
            <p class="confirmation-note mt-3">HoneyBee must confirm availability and the order. Bank transfer is the only payment method, and payment is confirmed separately by HoneyBee.</p>
            <a class="confirmation-link" href="{{ route('checkout') }}">Return to checkout</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
  window.location.replace(@json($whatsAppUrl));
</script>
@endpush
