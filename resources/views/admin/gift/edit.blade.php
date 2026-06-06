@extends('layouts.admin')

@section('title', 'Edit Gift Item')

@section('content')

<div class="topbar">
    <h1>Edit <span>Gift & Design</span> Item</h1>
    <a href="/admin/services">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="/admin/gift/{{ $gift->gift_design_id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Item Name *</label>
                <input type="text" name="item_name" value="{{ $gift->item_name }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Category *</label>
                <input type="text" name="category" value="{{ $gift->category }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Material</label>
                <input type="text" name="material" value="{{ $gift->material }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Size</label>
                <input type="text" name="size" value="{{ $gift->size }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Price (Rs.) *</label>
                <input type="number" name="price" value="{{ $gift->price }}" step="0.01" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Customization Option</label>
                <input type="text" name="customization_option" value="{{ $gift->customization_option }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Image</label>
                @if($gift->image)
                    <img src="{{ asset('images/gifts/' . $gift->image) }}" style="height: 80px; margin-bottom: 10px; border-radius: 8px;">
                @endif
                <input type="file" name="image" accept="image/*"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
            <textarea name="description" rows="4"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">{{ $gift->description }}</textarea>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit"
                style="background: #f5a623; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer;">
                ✅ Update Item
            </button>

            <a href="/admin/gift/{{ $gift->gift_design_id }}/delete"
                onclick="return confirm('Are you sure you want to delete this item?')"
                style="background: #dc3545; color: #fff; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                🗑️ Delete Item
            </a>
        </div>

    </form>
</div>

@endsection