@extends('layouts.admin')

@section('title', 'Add Gift Item')

@section('content')

<style>
    .gift-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .gift-form-field label { display: block; margin-bottom: 8px; font-weight: bold; }
    .gift-form-field input:not([type="file"]), .gift-form-field select, .gift-form-field textarea {
        width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;
    }
    .gift-form-field input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
    .gift-form-error { color: #842029; margin-top: 6px; }
    .gift-form-actions { margin-top: 30px; display: flex; flex-wrap: wrap; align-items: center; gap: 15px; }
    .gift-form-submit { background: #f5a623; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; }
    .gift-form-cancel { color: #1a1a1a; padding: 11px 24px; border: 1px solid #ddd; border-radius: 25px; font-weight: bold; text-decoration: none; }
    .gift-form-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; }
    @media (max-width: 768px) { .gift-form-grid { grid-template-columns: 1fr; } }
</style>

<div class="topbar">
    <h1>Add <span>Gift &amp; Design</span> Item</h1>
    <a href="{{ route('admin.services.gift') }}">&larr; Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    @if($errors->any())
        <div role="alert" style="background:#f8d7da;color:#842029;padding:15px;border-radius:8px;margin-bottom:20px;">
            <strong>Please correct the following:</strong>
            <ul style="margin:8px 0 0;padding-left:20px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div role="status" style="background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.gift.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($galleryImage)
            <input type="hidden" name="gallery_image_id" value="{{ $galleryImage->id }}">
            <input type="hidden" name="category" value="{{ $defaultCategory }}">
            <div class="d-flex align-items-center gap-3 mb-4 p-3" role="status" aria-live="polite" style="background:#fff8e8;border-radius:10px;">
                @if($galleryImageUrl = \App\Support\PublicImage::url($galleryImage->image))<img id="gift-image-preview" class="gift-form-preview" src="{{ $galleryImageUrl }}" alt="{{ $galleryImage->title ?? 'Selected gallery image' }}">@else<span id="gift-image-fallback" class="text-muted">Selected gallery image is missing.</span>@endif
                <p class="mb-0">Selected gallery image will be used unless you upload a replacement.</p>
            </div>
        @endif

        <div class="gift-form-grid">
            <div class="gift-form-field">
                <label for="item_name">Product Name *</label>
                <input id="item_name" type="text" name="item_name" value="{{ old('item_name', $galleryImage?->title) }}" required>
                @error('item_name')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="category">Category *</label>
                @if($galleryImage)
                    <select id="category" disabled aria-label="Selected category"><option selected>{{ $defaultCategory }}</option></select>
                @else
                    <select id="category" name="category" required>
                        <option value="">Select a category</option>
                        <option value="Gift" @selected(old('category') === 'Gift')>Gift</option>
                        <option value="Frame" @selected(old('category') === 'Frame')>Frame</option>
                    </select>
                @endif
                @error('category')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="material">Material</label>
                <input id="material" type="text" name="material" value="{{ old('material') }}">
                @error('material')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="size">Size</label>
                <input id="size" type="text" name="size" value="{{ old('size') }}">
                @error('size')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="price">Price (Rs.) *</label>
                <input id="price" type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
                @error('price')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="offer_price">Offer Price (Optional)</label>
                <input id="offer_price" type="number" name="offer_price" value="{{ old('offer_price') }}" min="0" step="0.01">
                @error('offer_price')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="customization_option">Customization</label>
                <input id="customization_option" type="text" name="customization_option" value="{{ old('customization_option') }}">
                @error('customization_option')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>

            <div class="gift-form-field">
                <label for="image">Product Image</label>
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-describedby="image-preview">
                <div id="image-preview" class="mt-2" aria-live="polite"></div>
                <small style="display:block;color:#666;margin-top:6px;">JPG, PNG, or WebP — maximum 10 MB. {{ $galleryImage ? 'Upload only to replace the selected gallery image.' : '' }}</small>
                @error('image')<div class="gift-form-error">{{ $message }}</div>@enderror
                @error('gallery_image_id')<div class="gift-form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="gift-form-field" style="margin-top:20px;">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description')<div class="gift-form-error">{{ $message }}</div>@enderror
        </div>

        <div class="gift-form-actions">
            <button type="submit" class="gift-form-submit">Add Item</button>
            <a class="gift-form-cancel" href="{{ route('admin.services.gift') }}">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('image')?.addEventListener('change', function () {
    const preview = document.getElementById('image-preview');
    preview.replaceChildren();
    const file = this.files?.[0];
    if (!file || !file.type.startsWith('image/')) return;
    const image = document.createElement('img');
    image.className = 'gift-form-preview';
    image.alt = 'Selected product image preview';
    image.src = URL.createObjectURL(file);
    preview.append(image);
});
</script>
@endpush

@endsection
