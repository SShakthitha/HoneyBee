@extends('layouts.admin')

@section('title', 'Businesses')

@section('content')
<div class="topbar">
    <h1>All <span>Businesses</span></h1>
</div>

<div class="admin-toolbar">
    <button type="button" class="btn btn-honey" data-bs-toggle="modal" data-bs-target="#addBusinessModal">
        <i class="fa-solid fa-plus me-1"></i> Add Business
    </button>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if($businesses->isEmpty())
            <div class="text-center text-muted py-5">No businesses yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle admin-datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($businesses as $b)
                        <tr>
                            <td>#{{ $b->business_id }}</td>
                            <td>{{ $b->business_name }}</td>
                            <td>{{ $b->contact_email }}</td>
                            <td>{{ $b->phone }}</td>
                            <td class="text-end">
                                <span class="action-buttons">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBusinessModal{{ $b->business_id }}"
                                            title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.businesses.delete', $b->business_id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Do you want to remove this business?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="addBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.businesses.store') }}">
                @csrf

                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Add Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="business_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="contact_email" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Save Business</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($businesses as $b)
<div class="modal fade" id="editBusinessModal{{ $b->business_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.businesses.update', $b->business_id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Edit Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="business_name" value="{{ $b->business_name }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="contact_email" value="{{ $b->contact_email }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ $b->phone }}" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control">{{ $b->description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Update Business</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection