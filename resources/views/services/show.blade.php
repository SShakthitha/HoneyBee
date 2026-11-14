@extends('layouts.app')

@section('title', $service->service_name ?: 'Service details')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($service->description ?: 'Explore this HoneyBee Gifts & Designs service and request an order.'), 155))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
@endpush

@section('content')
    <article class="honey-services container py-5">
        <nav class="honey-services__breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
            <a href="{{ route('services') }}">Services</a><span aria-hidden="true">/</span>
            <span aria-current="page">{{ $service->service_name ?: 'Service details' }}</span>
        </nav>

        <div class="honey-service-detail">
            <div class="honey-service-detail__content">
                <span class="honey-services__eyebrow">{{ $service->service_type }}</span>
                <h1>{{ $service->service_name ?: 'HoneyBee service' }}</h1>
                @if ($service->category)
                    <p class="honey-service-card__business">{{ $service->category }}</p>
                @endif
                <p class="honey-service-detail__description">{{ $service->description ?: 'Contact HoneyBee to discuss the details and options for this service.' }}</p>
                @if ($service->business?->business_name)
                    <p class="honey-service-card__business">Offered by {{ $service->business->business_name }}</p>
                @endif
            </div>
            <aside class="honey-service-detail__order">
                <span>Service price</span>
                <strong>Rs. {{ number_format((float) $service->price, 2) }}</strong>
                <p>Any payment or discount details will be confirmed by HoneyBee before fulfilment.</p>
                <a class="btn honey-service-card__link" href="{{ route('orders.create', $service->service_id) }}">Request this service</a>
                <a class="honey-service-detail__back" href="{{ route('services') }}">Back to services</a>
            </aside>
        </div>
    </article>
@endsection
