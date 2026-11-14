@extends('layouts.app')

@section('title', 'Services')
@section('meta_description', 'Explore HoneyBee services for custom gifts, event styling, and handcrafted design. Browse available services and request an order.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
@endpush

@section('content')
    <section class="honey-services container py-5">
        <header class="honey-services__heading">
            <span class="honey-services__eyebrow">Made with care</span>
            <h1>Our <span>Services</span></h1>
            <p>Browse the services currently offered by HoneyBee Gifts &amp; Designs.</p>
        </header>

        @if ($services->isNotEmpty())
            <div class="honey-services__grid">
                @foreach ($services as $service)
                    <article class="honey-service-card">
                        <div class="honey-service-card__topline">
                            <span class="honey-service-card__type">{{ $service->service_type }}</span>
                            @if ($service->category)
                                <span class="honey-service-card__category">{{ $service->category }}</span>
                            @endif
                        </div>
                        <h2><a href="{{ route('services.show', $service->service_id) }}">{{ $service->service_name ?: 'HoneyBee service' }}</a></h2>
                        <p class="honey-service-card__description">{{ $service->description ?: 'Talk with our team about the details and options for this service.' }}</p>
                        @if ($service->business?->business_name)
                            <p class="honey-service-card__business">Offered by {{ $service->business->business_name }}</p>
                        @endif
                        <div class="honey-service-card__footer">
                            <strong>Rs. {{ number_format((float) $service->price, 2) }}</strong>
                            <a class="btn honey-service-card__link" href="{{ route('services.show', $service->service_id) }}">
                                View details <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <nav class="honey-services__pagination" aria-label="Service pages">
                {{ $services->links() }}
            </nav>
        @else
            <div class="honey-services__empty">
                <h2>New services are on the way</h2>
                <p>Contact HoneyBee and we’ll help you plan a custom request.</p>
                <a class="btn honey-service-card__link" href="{{ route('contact') }}">Contact HoneyBee</a>
            </div>
        @endif
    </section>
@endsection
