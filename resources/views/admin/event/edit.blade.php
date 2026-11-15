@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="topbar">
    <h1>Edit <span>Event</span></h1>
    <a href="{{ route('admin.services.events') }}">Back to events</a>
</div>

<div class="admin-card form-box">
    <div class="admin-card-body">
        <form action="{{ route('admin.event.update', $event->event_id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
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
            <div class="col-12"><label class="form-label" for="event-image">Event image</label>
                @if($eventImageUrl = \App\Support\PublicImage::url($event->image))<div class="mb-2"><img id="event-image-current" src="{{ $eventImageUrl }}" alt="Current image for {{ $event->event_name }}" style="width:140px;height:120px;object-fit:cover;border-radius:8px;"></div>@elseif($event->image)<span class="text-muted d-block mb-2">Current image is missing.</span>@endif
                <input id="event-image" class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <div id="event-image-preview" class="mt-2" aria-live="polite"></div>
            </div>
            <div class="col-12"><button class="btn btn-honey" type="submit">Save changes</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('event-image')?.addEventListener('change', function () {
    const preview = document.getElementById('event-image-preview');
    preview.replaceChildren();
    const file = this.files?.[0];
    if (!file || !file.type.startsWith('image/')) return;
    const image = document.createElement('img');
    image.src = URL.createObjectURL(file);
    image.alt = 'Selected replacement event image preview';
    image.style.cssText = 'width:140px;height:120px;object-fit:cover;border-radius:8px';
    preview.append(image);
});
</script>
@endpush
@endsection
