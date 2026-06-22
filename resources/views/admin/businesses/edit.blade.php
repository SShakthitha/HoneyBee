@extends('layouts.admin')

@section('title', 'Edit Business')

@section('content')
<div class="topbar">
    <h1>Edit <span>Business</span></h1>
    <a href="{{ route('admin.businesses') }}">Back</a>
</div>

<div class="admin-card form-box">
    <div class="admin-card-body">
        <form method="POST" action="{{ route('admin.businesses.update', $business->business_id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="business_name" value="{{ $business->business_name }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="contact_email" value="{{ $business->contact_email }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ $business->phone }}" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ $business->description }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-honey">
                    Update Business
                </button>
            </div>
        </form>
    </div>
</div>
@endsection