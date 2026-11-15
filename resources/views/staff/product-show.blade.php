@extends('layouts.staff')
@php
    $labels = ['gift' => 'Gift', 'laser' => 'Laser Work', 'event' => 'Event'];
    $fields = match ($type) {
        'gift' => ['Name' => 'item_name', 'Category' => 'category', 'Material' => 'material', 'Size' => 'size', 'Customization' => 'customization_option', 'Description' => 'description'],
        'laser' => ['Name' => 'product_name', 'Laser type' => 'laser_type', 'Material type' => 'material_type', 'Product category' => 'product_category', 'Size' => 'size', 'Engraving text' => 'engraving_text', 'Description' => 'description'],
        'event' => ['Name' => 'event_name', 'Event type' => 'event_type', 'Event date' => 'event_date', 'Location' => 'event_location', 'Decoration type' => 'decoration_type', 'Description' => 'description'],
    };
@endphp
@section('title', $labels[$type].' Details')
@section('content')
<div class="page-head">
    <h1><span>{{ $labels[$type] }}</span> Details</h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-dark" href="{{ route('staff.products.category', $type) }}">Back to {{ $type === 'gift' ? 'Gifts' : ($type === 'event' ? 'Events' : 'Laser Work') }}</a>
        <a class="btn btn-honey" href="{{ route('staff.products.edit', [$type, $model->getKey()]) }}"><i class="fa-solid fa-pen-to-square me-1"></i> Edit</a>
    </div>
</div>
<section class="honey-card">
    <div class="row g-4">
        @if($imageUrl = \App\Support\PublicImage::url($model->image))
            <div class="col-12 col-md-4"><img src="{{ $imageUrl }}" alt="{{ $model->{$fields['Name']} }}" class="img-fluid rounded" style="max-height:360px;object-fit:contain"></div>
        @endif
        <div class="col">
            <dl class="row mb-0">
                @foreach($fields as $label => $field)
                    <dt class="col-sm-4 mb-2">{{ $label }}</dt>
                    <dd class="col-sm-8 mb-2">{{ $model->{$field} ?: '—' }}</dd>
                @endforeach
                @if($type === 'event')
                    @foreach(['lighting_service' => 'Lighting', 'sound_service' => 'Sound', 'dj_service' => 'DJ', 'photography_service' => 'Photography', 'cake_service' => 'Cake'] as $field => $label)
                        <dt class="col-sm-4 mb-2">{{ $label }}</dt><dd class="col-sm-8 mb-2">{{ $model->{$field} ? 'Yes' : 'No' }}</dd>
                    @endforeach
                @endif
                <dt class="col-sm-4 mb-2">Price</dt><dd class="col-sm-8 mb-2">Rs. {{ number_format((float) $model->price, 2) }}</dd>
                <dt class="col-sm-4 mb-2">Offer price</dt><dd class="col-sm-8 mb-2">{{ $model->offer_price !== null ? 'Rs. '.number_format((float) $model->offer_price, 2) : '—' }}</dd>
                <dt class="col-sm-4 mb-2">Created</dt><dd class="col-sm-8 mb-2">{{ $model->created_at?->format('d M Y') ?? '—' }}</dd>
            </dl>
        </div>
    </div>
</section>
@endsection
