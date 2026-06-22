@extends('layouts.admin')

@section('title', 'Staff')

@section('content')
<div class="topbar">
    <h1>All <span>Staff</span></h1>
</div>

<div class="admin-toolbar">
    <button type="button" class="btn btn-honey" data-bs-toggle="modal" data-bs-target="#addStaffModal">
        <i class="fa-solid fa-plus me-1"></i> Add Staff
    </button>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        @if($staff->isEmpty())
            <div class="text-center text-muted py-5">No staff members yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle admin-datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $s)
                        <tr>
                            <td>#{{ $s->staff_id }}</td>
                            <td>{{ $s->full_name }}</td>
                            <td>{{ $s->role }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->phone }}</td>
                            <td>{{ $s->hire_date }}</td>
                            <td class="text-end">
                                <span class="action-buttons">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editStaffModal{{ $s->staff_id }}"
                                            title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.staff.delete', $s->staff_id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Do you want to remove this staff member?');">
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

{{-- Add Staff Modal --}}
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Add Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" name="role" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" name="hire_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Save Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Staff Modals --}}
@foreach($staff as $s)
<div class="modal fade" id="editStaffModal{{ $s->staff_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.staff.update', $s->staff_id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Edit Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" value="{{ $s->full_name }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" name="role" value="{{ $s->role }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ $s->email }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="{{ $s->phone }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" name="hire_date" value="{{ $s->hire_date }}" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection