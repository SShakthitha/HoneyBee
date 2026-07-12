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
 
        <div class="table-responsive">
            <table id="staffTable" class="table table-striped table-hover align-middle">
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
                <tbody></tbody>
            </table>
        </div>
 
    </div>
</div>
 
{{-- ADD MODAL --}}
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Add Staff</h5>
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
                    <button type="submit" class="btn btn-honey">Save Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- EDIT MODAL (single, shared, populated via AJAX) --}}
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editStaffForm" method="POST">
                @csrf
                @method('PUT')
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Edit Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
 
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" id="edit_role" name="role" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" id="edit_hire_date" name="hire_date" class="form-control" required>
                        </div>
                    </div>
                </div>
 
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteStaffForm" method="POST">
                @csrf
                @method('DELETE')
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Delete Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
 
                <div class="modal-body">
                    Are you sure you want to remove this staff member? This can't be undone.
                </div>
 
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
@endsection
 
@push('scripts')
<script>
$(function () {
 
    // DataTable
    $('#staffTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.staff.data') }}",
        columns: [
            { data: 'staff_id', render: d => '#' + d },
            { data: 'full_name' },
            { data: 'role' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'hire_date' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
 
    // EDIT BUTTON CLICK
    $(document).on('click', '.editBtn', function () {
 
        let id = $(this).data('id');
 
        $.get("/admin/staff/" + id + "/edit", function (data) {
 
            $('#edit_full_name').val(data.full_name);
            $('#edit_role').val(data.role);
            $('#edit_email').val(data.email);
            $('#edit_phone').val(data.phone);
            $('#edit_hire_date').val(data.hire_date);
 
            $('#editStaffForm').attr('action', '/admin/staff/' + id);
 
            let modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            modal.show();
 
        });
 
    });
 
    // DELETE BUTTON CLICK
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        $('#deleteStaffForm').attr('action', '/admin/staff/' + id);
 
        let modal = new bootstrap.Modal(document.getElementById('deleteStaffModal'));
        modal.show();
    });
 
});
</script>
@endpush