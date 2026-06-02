@extends('layouts.admin')

@section('title', 'Add Gift Item')

@section('content')

<div class="topbar">
    <h1>Add <span>Gift & Design</span> Item</h1>
    <a href="/admin/services">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="/admin/gift/store" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Item Name *</label>
                <input type="text" name="item_name" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Category *</label>
                <input type="text" name="category" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Material</label>
                <input type="text" name="material"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Size</label>
                <input type="text" name="size"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Price (Rs.) *</label>
                <input type="number" name="price" step="0.01" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Customization Option</label>
                <input type="text" name="customization_option"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Service *</label>
                <select name="service_id" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="">Select Service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Image</label>
                <input type="file" name="image" accept="image/*"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
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
                ➕ Add Item
            </button>
        </div>

    </form>
</div>

@endsection