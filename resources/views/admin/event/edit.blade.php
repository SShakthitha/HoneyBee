@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="topbar">
    <h1>Edit <span>Event</span></h1>
    <a href="{{ route('admin.services.events') }}">Back to events</a>
</div>

<div class="admin-card form-box">
    <div class="admin-card-body">
        <form action="{{ route('admin.event.update', $event->event_id) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6"><label class="form-label">Event name</label><input class="form-control" name="event_name" value="{{ old('event_name', $event->event_name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Event type</label><input class="form-control" name="event_type" value="{{ old('event_type', $event->event_type) }}" required></div>
            <div class="col-md-6"><label class="form-label">Event date</label><input class="form-control" type="date" name="event_date" value="{{ old('event_date', optional($event->event_date)->format('Y-m-d') ?? $event->event_date) }}" required></div>
            <div class="col-md-6"><label class="form-label">Location</label><input class="form-control" name="event_location" value="{{ old('event_location', $event->event_location) }}"></div>
            <div class="col-md-6"><label class="form-label">Decoration type</label><input class="form-control" name="decoration_type" value="{{ old('decoration_type', $event->decoration_type) }}"></div>
            <div class="col-md-3"><label class="form-label">Price</label><input class="form-control" type="number" min="0" step="0.01" name="price" value="{{ old('price', $event->price) }}" required></div>
            <div class="col-md-3"><label class="form-label">Offer price</label><input class="form-control" type="number" min="0" step="0.01" name="offer_price" value="{{ old('offer_price', $event->offer_price) }}"></div>
            <div class="col-12">
                @foreach (['lighting_service' => 'Lighting', 'sound_service' => 'Sound', 'dj_service' => 'DJ', 'photography_service' => 'Photography', 'cake_service' => 'Cake'] as $field => $label)
                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $event->$field))><label class="form-check-label">{{ $label }}</label></div>
                @endforeach
            </div>
            <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" rows="4" name="description">{{ old('description', $event->description) }}</textarea></div>
            <div class="col-12"><button class="btn btn-honey" type="submit">Save changes</button></div>
        </form>
    </div>
</div>
@endsection
