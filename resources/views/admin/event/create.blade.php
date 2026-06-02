@extends('layouts.admin')

@section('title', 'Add Event')

@section('content')

<div class="topbar">
    <h1>Add <span>Event</span></h1>
    <a href="/admin/services">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">

    <form action="/admin/event/store" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Name *</label>
                <input type="text" name="event_name" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Type *</label>
                <input type="text" name="event_type" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Date *</label>
                <input type="date" name="event_date" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Event Location</label>
                <input type="text" name="event_location"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Decoration Type</label>
                <input type="text" name="decoration_type"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Price (Rs.) *</label>
                <input type="number" name="price" step="0.01" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Service *</label>
                <select name="service_id" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="">Select Service</option>
                </select>
            </div>

        </div>

        <!-- Services Checkboxes -->
        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 15px; font-weight: bold;">Additional Services</label>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <label><input type="checkbox" name="lighting_service"> 💡 Lighting</label>
                <label><input type="checkbox" name="sound_service"> 🔊 Sound</label>
                <label><input type="checkbox" name="dj_service"> 🎧 DJ</label>
                <label><input type="checkbox" name="photography_service"> 📸 Photography</label>
                <label><input type="checkbox" name="cake_service"> 🎂 Cake</label>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
            <textarea name="description" rows="4"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;"></textarea>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit"
                style="background: #f5a623; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer;">
                ➕ Add Event
            </button>
        </div>

    </form>
</div>

@endsection