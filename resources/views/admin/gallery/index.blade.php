@extends('layouts.admin')

@section('title', 'Gallery Management')

@section('content')
<div class="topbar">
    <h1>Gallery <span>Management</span></h1>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.services') }}">Back to Services</a>
        <a href="{{ route('admin.gallery.create') }}">Upload Image</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($images->isEmpty())
    <p>No gallery images yet.</p>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;">
        @foreach($images as $image)
            <div style="background:#fff;border-radius:12px;padding:12px;box-shadow:0 3px 14px rgba(0,0,0,.08);">
                <img src="{{ asset('storage/'.$image->image) }}" alt="{{ $image->title ?? $image->category.' image' }}" style="width:100%;height:220px;object-fit:cover;border-radius:8px;">
                <h3 style="margin:10px 0 4px;">{{ $image->title ?? 'Untitled image' }}</h3>
                <p>{{ $image->category }}</p>
                @if(in_array($image->category, ['Gift & Design', 'Frame Designs'], true))
                    <a href="{{ route('admin.gift.create', ['gallery_image_id' => $image->id]) }}">
                        Add to {{ $image->category === 'Frame Designs' ? 'Frames' : 'Gifts' }}
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@endif
@endsection
