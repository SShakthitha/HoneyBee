@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="topbar">
    <h1>All <span>Customers</span></h1>
</div>

<div class="admin-toolbar">
    <button type="button" class="btn btn-honey" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
        <i class="fa-solid fa-plus me-1"></i> Add Customer
    </button>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if($customers->isEmpty())
            <div class="text-center text-muted py-5">No customers yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle admin-datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $c)
                        <tr>
                            <td>#{{ $c->customer_id }}</td>
                            <td>{{ $c->full_name }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->phone }}</td>
                            <td>{{ $c->address ?? '—' }}</td>
                            <td class="text-end">
                                <span class="action-buttons">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCustomerModal{{ $c->customer_id }}"
                                            title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.customers.delete', $c->customer_id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Do you want to remove this customer?');">
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

{{-- Add Customer Modal --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.customers.store') }}">
                @csrf
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Customer Modals --}}
@foreach($customers as $c)
<div class="modal fade" id="editCustomerModal{{ $c->customer_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.customers.update', $c->customer_id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" value="{{ $c->full_name }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ $c->email }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ $c->phone }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="4" class="form-control">{{ $c->address }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection