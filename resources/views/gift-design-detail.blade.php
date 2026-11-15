@extends('layouts.app')

@section('title', $gift->item_name)
@section('meta_description', $gift->description ?: $gift->item_name.' from HoneyBee Gift & Design.')

@push('styles')
<style>
  .product-detail{max-width:1120px;margin:3rem auto;padding:0 1.25rem}.product-detail__card{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:2rem;background:#fff;border:1px solid #eee5d5;border-radius:18px;padding:clamp(1rem,4vw,2.5rem);box-shadow:0 8px 28px rgba(0,0,0,.08)}.product-detail__image{width:100%;max-height:520px;aspect-ratio:1/1;object-fit:contain;border-radius:12px;background:#fff8ea}.product-detail__placeholder{min-height:320px;display:grid;place-items:center;border-radius:12px;background:#fff8ea;color:#806a48}.product-detail__body{align-self:center}.product-detail__body h1{font-size:clamp(2rem,4vw,3rem);margin:.5rem 0 1rem}.product-detail__price{font-size:1.3rem;font-weight:700;color:#9a5c00}.product-detail__actions{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.5rem}.product-detail__back{display:inline-block;margin-bottom:1rem;color:#6d4c13}@media(max-width:760px){.product-detail__card{grid-template-columns:1fr}.product-detail__placeholder{min-height:220px}}
</style>
@endpush

@section('content')
@php($imageUrl = \App\Support\PublicImage::url($gift->image))
<main class="product-detail">
  <a class="product-detail__back" href="{{ route('gift.design') }}">← Back to Gift &amp; Design</a>
  <article class="product-detail__card">
    <div>
      @if($imageUrl)
        <img class="product-detail__image" src="{{ $imageUrl }}" alt="{{ $gift->item_name }}">
      @else
        <div class="product-detail__placeholder">Product image not available</div>
      @endif
    </div>
    <div class="product-detail__body">
      <p class="section-eyebrow">{{ $gift->category }}</p>
      <h1>{{ $gift->item_name }}</h1>
      <p>{{ $gift->description ?: 'Contact us to discuss this product and customization options.' }}</p>
      @if($gift->material)<p><strong>Material:</strong> {{ $gift->material }}</p>@endif
      @if($gift->size)<p><strong>Size:</strong> {{ $gift->size }}</p>@endif
      @if($gift->customization_option)<p><strong>Customization:</strong> {{ $gift->customization_option }}</p>@endif
      <p class="product-detail__price">@if($gift->hasValidOfferPrice())<del class="text-muted">Rs. {{ number_format((float)$gift->price,2) }}</del> Rs. {{ number_format((float)$gift->offer_price,2) }}@else Rs. {{ number_format((float)$gift->price,2) }}@endif</p>
      <div class="product-detail__actions">
        <form method="POST" action="{{ route('orders.catalog.store') }}" class="d-flex gap-2 align-items-center">@csrf<input type="hidden" name="item_type" value="{{ in_array(strtolower(trim($gift->category)), ['frame', 'frames'], true) ? 'frame' : 'gift' }}"><input type="hidden" name="item_id" value="{{ $gift->gift_design_id }}"><input type="hidden" name="submission_token" value="{{ $submissionToken }}"><label class="visually-hidden" for="gift-quantity">Quantity</label><input id="gift-quantity" class="form-control" type="number" name="quantity" min="1" value="1" style="width:90px"><button class="btn btn-warning">Order</button></form>
        <a class="btn btn-primary" href="{{ route('gift.design') }}#gifts">Browse more products</a>
        <a class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ config('honeybee.contact.whatsapp_number') }}?text={{ rawurlencode('Hello HoneyBee, I would like to enquire about '.$gift->item_name.'.') }}">Ask about this product</a>
      </div>
    </div>
  </article>
</main>
@endsection
