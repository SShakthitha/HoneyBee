@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')

<div class="topbar">
    <h1>Edit <span>Event</span></h1>
    <a href="/admin/services">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">

    <form action="/admin/event/{{ $event->event_id }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Name *</label>
                <input type="text" name="event_name" value="{{ $event->event_name }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Type *</label>
                <input type="text" name="event_type" value="{{ $event->event_type }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Date *</label>
                <input type="date" name="event_date" value="{{ $event->event_date }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Location</label>
                <input type="text" name="event_location" value="{{ $event->event_location }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Decoration Type</label>
                <input type="text" name="decoration_type" value="{{ $event->decoration_type }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Price (Rs.) *</label>
                <input type="number" name="price" value="{{ $event->price }}" step="0.01" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 15px; font-weight: bold;">Additional Services</label>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <label><input type="checkbox" name="lighting_service" {{ $event->lighting_service ? 'checked' : '' }}> 💡 Lighting</label>
                <label><input type="checkbox" name="sound_service" {{ $event->sound_service ? 'checked' : '' }}> 🔊 Sound</label>
                <label><input type="checkbox" name="dj_service" {{ $event->dj_service ? 'checked' : '' }}> 🎧 DJ</label>
                <label><input type="checkbox" name="photography_service" {{ $event->photography_service ? 'checked' : '' }}> 📸 Photography</label>
                <label><input type="checkbox" name="cake_service" {{ $event->cake_service ? 'checked' : '' }}> 🎂 Cake</label>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
            <textarea name="description" rows="4"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">{{ $event->description }}</textarea>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit"
                style="background: #f5a623; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer;">
                ✅ Update Event
            </button>

            <a href="/admin/event/{{ $event->event_id }}/delete"
                onclick="return confirm('Are you sure you want to delete this event?')"
                style="background: #dc3545; color: #fff; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                🗑️ Delete Event
            </a>
        </div>

    </form>
</div>

@endsection