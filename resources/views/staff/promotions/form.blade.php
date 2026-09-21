@extends('layouts.staff')

@section('title', isset($promotion) ? 'Edit Promotion' : 'Create Promotion')

@section('content')
<div class="page-head">
    <h1>{{ isset($promotion) ? 'Edit' : 'Add' }} <span>Promotion</span></h1>
    <a href="{{ route('staff.promotions.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<section class="honey-card">
    <form action="{{ isset($promotion) ? route('staff.promotions.update', $promotion) : route('staff.promotions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($promotion))
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $promotion->title ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Button text</label>
                <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $promotion->button_text ?? 'Shop Now') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Short description</label>
                <textarea name="short_description" class="form-control" rows="3">{{ old('short_description', $promotion->short_description ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Destination / route</label>
                <input type="text" name="destination" class="form-control" value="{{ old('destination', $promotion->destination ?? 'gift.design') }}" placeholder="gift.design, events, laser.work, home">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" @selected(old('is_active', $promotion->is_active ?? true))>Active</option>
                    <option value="0" @selected(! old('is_active', $promotion->is_active ?? true))>Inactive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Start date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($promotion->start_date ?? null)?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">End date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($promotion->end_date ?? null)?->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                @if(!empty($promotion->image))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $promotion->image) }}" alt="Promotion image" style="width:120px;height:120px;object-fit:cover;border-radius:10px;">
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-honey">{{ isset($promotion) ? 'Update Promotion' : 'Create Promotion' }}</button>
        </div>
    </form>
</section>
@endsection
