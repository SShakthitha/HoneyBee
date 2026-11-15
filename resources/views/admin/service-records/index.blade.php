@extends('layouts.admin')

@section('title', 'Bookable Services')

@section('content')
<div class="topbar">
    <h1>Bookable <span>Services</span></h1>
    <a href="{{ route('admin.service-records.create') }}" class="btn btn-honey"><i class="fa-solid fa-plus me-1"></i> Add Service</a>
</div>

@if ($errors->has('service'))
    <div class="alert alert-danger" role="alert">{{ $errors->first('service') }}</div>
@endif

<div class="admin-card">
    <div class="admin-card-body">
        @if ($services->isEmpty())
            <p class="mb-0">No bookable services have been entered yet. Add only approved service details and pricing.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Name</th><th>Type</th><th>Business</th><th>Price</th><th>Availability</th><th>Category</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>{{ $service->service_name }}</td>
                            <td>{{ $service->service_type }}</td>
                            <td>{{ $service->business?->business_name }}</td>
                            <td>Rs. {{ number_format((float) $service->price, 2) }}</td>
                            <td><span class="badge {{ $service->availability_status === 'available' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($service->availability_status) }}</span></td>
                            <td>{{ $service->category ?: '—' }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.service-records.edit', $service->service_id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.service-records.destroy', $service->service_id) }}" class="d-inline" onsubmit="return confirm('Delete this service record?');">
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
            {{ $services->links() }}
        @endif
    </div>
</div>
@endsection
