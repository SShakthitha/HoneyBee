@extends('layouts.admin')

@section('title', 'Edit Gift Item')

@section('content')

<div class="topbar">
    <h1>Edit <span>Gift & Design</span> Item</h1>
    <a href="/admin/gift">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px;">

    <form action="/admin/gift/{{ $gift->gift_design_id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

            <input type="text" name="item_name" value="{{ $gift->item_name }}" required>
            <input type="text" name="category" value="{{ $gift->category }}" required>
            <input type="number" name="price" value="{{ $gift->price }}" required>

            <input type="number" name="offer_price" value="{{ $gift->offer_price }}">

        </div>

        <textarea name="description">{{ $gift->description }}</textarea>

        <button type="submit">Update</button>
    </form>

</div>

@endsection