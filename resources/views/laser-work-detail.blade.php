@extends('layouts.app')

@section('title', $laserWork->product_name)
@section('meta_description', $laserWork->description ?: $laserWork->product_name.' from HoneyBee Laser Work.')

@push('styles')
<style>
  .product-detail{max-width:1120px;margin:3rem auto;padding:0 1.25rem}.product-detail__card{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:2rem;background:#fff;border:1px solid #eee5d5;border-radius:18px;padding:clamp(1rem,4vw,2.5rem);box-shadow:0 8px 28px rgba(0,0,0,.08)}.product-detail__image{width:100%;max-height:520px;aspect-ratio:1/1;object-fit:contain;border-radius:12px;background:#fff8ea}.product-detail__placeholder{min-height:320px;display:grid;place-items:center;border-radius:12px;background:#fff8ea;color:#806a48}.product-detail__body{align-self:center}.product-detail__body h1{font-size:clamp(2rem,4vw,3rem);margin:.5rem 0 1rem}.product-detail__price{font-size:1.3rem;font-weight:700;color:#9a5c00}.product-detail__actions{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.5rem}.product-detail__back{display:inline-block;margin-bottom:1rem;color:#6d4c13}@media(max-width:760px){.product-detail__card{grid-template-columns:1fr}.product-detail__placeholder{min-height:220px}}
</style>
@endpush

@section('content')
@php($imageUrl = \App\Support\PublicImage::url($laserWork->image))
<main class="product-detail">
  <a class="product-detail__back" href="{{ route('laser.work') }}">← Back to Laser Work</a>
  <article class="product-detail__card">
    <div>
      @if($imageUrl)
        <img class="product-detail__image" src="{{ $imageUrl }}" alt="{{ $laserWork->product_name }}">
      @else
        <div class="product-detail__placeholder">Product image not available</div>
      @endif
    </div>
    <div class="product-detail__body">
      <p class="section-eyebrow">{{ $laserWork->laser_type }}</p>
      <h1>{{ $laserWork->product_name }}</h1>
      <p>{{ $laserWork->description ?: 'Contact us to discuss sizes, materials, and pricing.' }}</p>
      @if($laserWork->material_type)<p><strong>Material:</strong> {{ $laserWork->material_type }}</p>@endif
      @if($laserWork->product_category)<p><strong>Category:</strong> {{ $laserWork->product_category }}</p>@endif
      @if($laserWork->size)<p><strong>Size:</strong> {{ $laserWork->size }}</p>@endif
      @if($laserWork->engraving_text)<p><strong>Engraving:</strong> {{ $laserWork->engraving_text }}</p>@endif
      <p class="product-detail__price">@if($laserWork->hasValidOfferPrice())<del class="text-muted">Rs. {{ number_format((float)$laserWork->price,2) }}</del> Rs. {{ number_format((float)$laserWork->offer_price,2) }}@else Rs. {{ number_format((float)$laserWork->price,2) }}@endif</p>
      @if($laserWork->production_type === 'custom_made')<p class="text-info">Made to order</p>@endif
      <div class="product-detail__actions">
        <form method="POST" action="{{ route('orders.catalog.store') }}" class="d-flex gap-2 align-items-center">@csrf<input type="hidden" name="item_type" value="laser"><input type="hidden" name="item_id" value="{{ $laserWork->laser_id }}"><input type="hidden" name="submission_token" value="{{ $submissionToken }}"><label class="visually-hidden" for="laser-quantity">Quantity</label><input id="laser-quantity" class="form-control" type="number" name="quantity" min="1" value="1" style="width:90px"><button class="btn btn-warning">Order</button></form>
        <a class="btn btn-primary" href="{{ route('laser.work') }}#products">Browse more products</a>
        <a class="btn btn-whatsapp" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ config('honeybee.contact.whatsapp_number') }}?text={{ rawurlencode('Hello HoneyBee Laser Works, I would like to enquire about '.$laserWork->product_name.'.') }}">Ask about this product</a>
      </div>
    </div>
  </article>
</main>
@endsection
