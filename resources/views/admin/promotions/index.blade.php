@extends('layouts.admin')

@section('title', 'Promotions')

@section('content')
<div class="topbar">
    <h1>Promotions <span>Management</span></h1>
    <a href="{{ route('admin.promotions.create') }}">+ Add Promotion</a>
</div>

<div class="admin-card">
    <div class="admin-card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Active and scheduled offers</h2>
    </div>
    <div class="admin-card-body">
        @if($promotions->isEmpty())
            <p class="mb-0 text-muted">No promotions have been created yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover admin-datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>CTA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promotion)
                            <tr>
                                <td><strong>{{ $promotion->title }}</strong><br><small class="text-muted">{{ Str::limit($promotion->short_description ?? '', 80) }}</small></td>
                                <td>@if($promotion->image)<img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;">@else<span class="text-muted">No image</span>@endif</td>
                                <td>{{ $promotion->start_date?->format('d M Y') ?? '—' }}<br>{{ $promotion->end_date?->format('d M Y') ?? '—' }}</td>
                                <td>@if($promotion->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                                <td>{{ $promotion->button_text ?? 'Shop Now' }}</td>
                                <td class="action-buttons">
                                    <a href="{{ route('admin.promotions.edit', $promotion) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" onsubmit="return confirm('Delete this promotion?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
