@extends('layouts.admin')

@section('title', 'Edit Laser Work')

@section('content')

<div class="topbar">
    <h1>Edit <span>Laser Work</span> Item</h1>
    <a href="{{ route('admin.services.laser') }}">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">

    <form action="{{ route('admin.laser.update', $laserWork->laser_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Product Name *</label>
                <input type="text" name="product_name" value="{{ $laserWork->product_name }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Laser Type *</label>
                <input type="text" name="laser_type" value="{{ $laserWork->laser_type }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Material Type</label>
                <input type="text" name="material_type" value="{{ $laserWork->material_type }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Product Category</label>
                <input type="text" name="product_category" value="{{ $laserWork->product_category }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Size</label>
                <input type="text" name="size" value="{{ $laserWork->size }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Price (Rs.) *</label>
                <input type="number" name="price" value="{{ $laserWork->price }}" step="0.01" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;" for="laser-image">Product Image</label>
            @if($laserImageUrl = \App\Support\PublicImage::url($laserWork->image))
                <img id="laser-image-current" src="{{ $laserImageUrl }}" alt="Current image for {{ $laserWork->product_name }}" style="width:120px;height:120px;object-fit:cover;border-radius:8px;margin-bottom:10px;">
            @elseif($laserWork->image)
                <span class="text-muted d-block mb-2">Current image is missing.</span>
            @endif
            <input id="laser-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" style="width:100%;padding:10px;">
            <div id="laser-image-preview" class="mt-2" aria-live="polite"></div>
        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Engraving Text</label>
            <textarea name="engraving_text" rows="3"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">{{ $laserWork->engraving_text }}</textarea>
        </div>

        <div style="margin-top: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
            <textarea name="description" rows="4"
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">{{ $laserWork->description }}</textarea>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit"
                style="background: #f5a623; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer;">
                ✅ Update Item
            </button>

            <form action="{{ route('admin.laser.destroy', $laserWork->laser_id) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this item?');"
                  style="display: inline;">

                @csrf
                @method('DELETE')

                <button type="submit"
                    style="background: #dc3545; color: #fff; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer;">
                    🗑 Delete Item
                </button>

            </form>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.getElementById('laser-image')?.addEventListener('change', function () {
    const preview = document.getElementById('laser-image-preview');
    preview.replaceChildren();
    const file = this.files?.[0];
    if (!file || !file.type.startsWith('image/')) return;
    const image = document.createElement('img');
    image.src = URL.createObjectURL(file);
    image.alt = 'Selected replacement laser image preview';
    image.style.cssText = 'width:120px;height:120px;object-fit:cover;border-radius:8px';
    preview.append(image);
});
</script>
@endpush

@endsection
