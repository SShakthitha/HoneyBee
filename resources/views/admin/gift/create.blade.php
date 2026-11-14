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

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/admin/gift/store" method="POST" enctype="multipart/form-data">
        @csrf
        @if($galleryImage)
            <input type="hidden" name="gallery_image_id" value="{{ $galleryImage->id }}">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding:12px;background:#fff8e8;border-radius:10px;">
                <img src="{{ asset('storage/'.$galleryImage->image) }}" alt="{{ $galleryImage->title ?? 'Selected gallery image' }}" style="width:100px;height:100px;object-fit:cover;border-radius:8px;">
                <p style="margin:0;">Selected gallery image will be used. Add the product price below.</p>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label>Item Name *</label>
                <input type="text" name="item_name" value="{{ old('item_name', $galleryImage?->title) }}" required style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Category *</label>
                <select name="category" required style="width:100%;padding:10px;">
                    <option value="Gift" @selected(old('category', $galleryImage ? $defaultCategory : '') === 'Gift')>Gift</option>
                    <option value="Frame" @selected(old('category', $galleryImage ? $defaultCategory : '') === 'Frame')>Frame</option>
                </select>
            </div>

            <div>
                <label>Material</label>
                <input type="text" name="material" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Size</label>
                <input type="text" name="size" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Price *</label>
                <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" required style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Customization</label>
                <input type="text" name="customization_option" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Offer Price</label>
                <input type="number" name="offer_price" value="{{ old('offer_price') }}" min="0" step="0.01" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Image</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="width:100%;padding:10px;">
                <small>JPG, PNG, or WebP — maximum 10 MB. {{ $galleryImage ? 'Upload only to replace the selected gallery image.' : '' }}</small>
            </div>

        </div>

        <div style="margin-top: 20px;">
            <label>Description</label>
            <textarea name="description" rows="4" style="width:100%;padding:10px;">{{ old('description') }}</textarea>
        </div>

        <button type="submit" style="margin-top:20px;padding:12px 25px;background:#f5a623;">
            ➕ Add Item
        </button>

    </form>
</div>

@endsection