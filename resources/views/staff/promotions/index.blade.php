@extends('layouts.staff')

@section('title', 'Promotions')

@section('content')
<div class="page-head">
    <h1>Promotions <span>Manager</span></h1>
    <a href="{{ route('staff.promotions.create') }}" class="btn btn-honey">+ Add Promotion</a>
</div>

<section class="honey-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Current campaign offers</h2>
    </div>
    @if($promotions->isEmpty())
        <p class="text-muted mb-0">No promotions yet.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Dates</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $promotion)
                        <tr>
                            <td><strong>{{ $promotion->title }}</strong><br><small class="text-muted">{{ $promotion->short_description ?? '-' }}</small></td>
                            <td>@if($promotion->image)<img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;">@else<span class="text-muted">No image</span>@endif</td>
                            <td>{{ $promotion->start_date?->format('d M Y') ?? '—' }}<br>{{ $promotion->end_date?->format('d M Y') ?? '—' }}</td>
                            <td>@if($promotion->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                            <td class="text-end">
                                <a href="{{ route('staff.promotions.edit', $promotion) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('staff.promotions.destroy', $promotion) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this promotion?');">
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
</section>
@endsection
