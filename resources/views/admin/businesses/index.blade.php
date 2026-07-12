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
 
        <div class="table-responsive">
            <table id="businessesTable" class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
 
    </div>
</div>
 
{{-- ADD MODAL --}}
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
 
{{-- EDIT MODAL (single, shared, populated via AJAX) --}}
<div class="modal fade" id="editBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editBusinessForm" method="POST">
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
                            <input type="text" id="edit_business_name" name="business_name" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="edit_contact_email" name="contact_email" class="form-control" required>
                        </div>
 
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" required>
                        </div>
 
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea id="edit_description" name="description" rows="4" class="form-control"></textarea>
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
 
{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteBusinessForm" method="POST">
                @csrf
                @method('DELETE')
 
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Delete Business</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
 
                <div class="modal-body">
                    Are you sure you want to delete this business? This can't be undone.
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
    $('#businessesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.businesses.data') }}",
        columns: [
            { data: 'business_id', render: d => '#' + d },
            { data: 'business_name' },
            { data: 'contact_email' },
            { data: 'phone' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
 
    // EDIT BUTTON CLICK
    $(document).on('click', '.editBtn', function () {
 
        let id = $(this).data('id');
 
        $.get("/admin/businesses/" + id + "/edit", function (data) {
 
            $('#edit_business_name').val(data.business_name);
            $('#edit_contact_email').val(data.contact_email);
            $('#edit_phone').val(data.phone);
            $('#edit_description').val(data.description);
 
            $('#editBusinessForm').attr('action', '/admin/businesses/' + id);
 
            let modal = new bootstrap.Modal(document.getElementById('editBusinessModal'));
            modal.show();
 
        });
 
    });
 
    // DELETE BUTTON CLICK
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');
        $('#deleteBusinessForm').attr('action', '/admin/businesses/' + id);
 
        let modal = new bootstrap.Modal(document.getElementById('deleteBusinessModal'));
        modal.show();
    });
 
});
</script>
@endpush