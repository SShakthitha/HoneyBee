@extends('layouts.admin')

@section('title', 'Edit Gift Item')

@section('content')

<div class="topbar">
    <h1>Edit <span>Gift & Design</span> Item</h1>
    <a href="{{ route('admin.gift.index') }}">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px;">

    <form action="{{ route('admin.gift.update', $gift->gift_design_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

            <input type="text" name="item_name" value="{{ old('item_name', $gift->item_name) }}" required>
            <select name="category" required>
                <option value="Gift" @selected(old('category', $gift->category) === 'Gift')>Gift</option>
                <option value="Frame" @selected(old('category', $gift->category) === 'Frame')>Frame</option>
            </select>
            <input type="number" name="price" value="{{ $gift->price }}" required>

            <input type="number" name="offer_price" value="{{ $gift->offer_price }}">
            <input type="text" name="material" value="{{ old('material', $gift->material) }}" placeholder="Material">
            <input type="text" name="size" value="{{ old('size', $gift->size) }}" placeholder="Size">
            <input type="text" name="customization_option" value="{{ old('customization_option', $gift->customization_option) }}" placeholder="Customization">
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <small style="color:#666;">JPG, PNG, or WebP — maximum 10 MB.</small>

        </div>

        <textarea name="description">{{ $gift->description }}</textarea>

        <button type="submit">Update</button>
    </form>

</div>

@endsection
