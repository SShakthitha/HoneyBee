@extends('layouts.admin')

@section('title', 'Edit Gallery Image')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Gallery Image</h3>

        <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.gallery.update', $image->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>

                    <input type="text"
                           name="title"
                           class="form-control"
                           value="{{ old('title', $image->title) }}"
                           placeholder="Optional title">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>

                    <select name="category" class="form-select" required>
                        <option value="Gift & Design"
                            @selected(old('category', $image->category) === 'Gift & Design')>
                            Gift & Design
                        </option>

                        <option value="Frame Designs"
                            @selected(old('category', $image->category) === 'Frame Designs')>
                            Frame Designs
                        </option>

                        <option value="Laser Work"
                            @selected(old('category', $image->category) === 'Laser Work')>
                            Laser Work
                        </option>

                        <option value="Events"
                            @selected(old('category', $image->category) === 'Events')>
                            Events
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label>

                    <div>
                        <img src="{{ asset('storage/'.$image->image) }}"
                             alt="{{ $image->title ?? 'Gallery image' }}"
                             style="width: 250px; height: 180px; object-fit: cover; border-radius: 10px;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Replace Image</label>

                    <input type="file"
                           name="image"
                           class="form-control"
                           accept="image/jpeg,image/png,image/webp">

                    <small class="text-muted">
                        Leave empty to keep the current image.
                    </small>
                </div>

                <button type="submit" class="btn btn-honey">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Update Image
                </button>

            </form>

        </div>
    </div>

</div>

@endsection