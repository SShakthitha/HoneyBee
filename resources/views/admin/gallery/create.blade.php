@extends('layouts.admin')

@section('title', 'Upload Gallery Image')

@section('content')
<div class="topbar">
    <h1>Upload <span>Gallery Image</span></h1>
    <a href="{{ route('admin.gallery.index') }}">Back to Gallery</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" style="background:#fff;padding:24px;border-radius:12px;">
    @csrf
    <div style="margin-bottom:16px;">
        <label for="title">Title</label>
        <input id="title" name="title" value="{{ old('title') }}" style="display:block;width:100%;padding:10px;">
    </div>
    <div style="margin-bottom:16px;">
        <label for="category">Category *</label>
        <select id="category" name="category" required style="display:block;width:100%;padding:10px;">
            <option value="">Select category</option>
            @foreach(['Gift & Design', 'Frame Designs', 'Laser Work', 'Events'] as $category)
                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
    </div>
    <div style="margin-bottom:16px;">
        <label for="image">Image *</label>
        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" required style="display:block;width:100%;padding:10px;">
        <small>JPG, PNG, or WebP; maximum 2 MB.</small>
    </div>
    <button type="submit">Upload Image</button>
</form>
@endsection
