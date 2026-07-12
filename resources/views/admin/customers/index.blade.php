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
 
        <div class="table-responsive">
            <table id="customersTable" class="table table-striped table-hover align-middle">
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
                <tbody></tbody>
            </table>
        </div>
 
    </div>
</div>
 
{{-- ADD MODAL --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
 
            <form method="POST" action="{{ route('admin.customers.store') }}">
                @csrf
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
 
                <div class="modal-body">
                    <div class="row g-3">
 
                        <div class="col-md-6">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
 
                        <div class="col-12">
                            <label>Address</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>
 
                    </div>
                </div>
 
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Save</button>
                </div>
 
            </form>
 
        </div>
    </div>
</div>
 
{{-- EDIT MODAL --}}
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
 
            <form id="editCustomerForm" method="POST">
                @csrf
                @method('PUT')
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
 
                <div class="modal-body">
 
                    <div class="row g-3">
 
                        <div class="col-md-6">
                            <label>Full Name</label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control">
                        </div>
 
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control">
                        </div>
 
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control">
                        </div>
 
                        <div class="col-12">
                            <label>Address</label>
                            <textarea id="edit_address" name="address" class="form-control"></textarea>
                        </div>
 
                    </div>
 
                </div>
 
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
 
            </form>
 
        </div>
    </div>
</div>
 
{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
 
            <form id="deleteCustomerForm" method="POST">
                @csrf
                @method('DELETE')
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title">Delete Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
 
                <div class="modal-body">
                    Are you sure you want to delete this customer? This can't be undone.
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
    $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.customers.data') }}",
        columns: [
            { data: 'customer_id', render: d => '#' + d },
            { data: 'full_name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'address', defaultContent: '—' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
 
    // EDIT BUTTON CLICK
    $(document).on('click', '.editBtn', function () {
 
        let id = $(this).data('id');
 
        $.get("/admin/customers/" + id + "/edit", function (data) {
 
            $('#edit_full_name').val(data.full_name);
            $('#edit_email').val(data.email);
            $('#edit_phone').val(data.phone);
            $('#edit_address').val(data.address);
 
            $('#editCustomerForm').attr('action', '/admin/customers/' + id);
 
            let modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
            modal.show();
 
        });
 
    });
 
    // DELETE BUTTON CLICK
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        $('#deleteCustomerForm').attr('action', '/admin/customers/' + id);
 
        let modal = new bootstrap.Modal(document.getElementById('deleteCustomerModal'));
        modal.show();
    });
 
});
</script>
 
@endpush
 