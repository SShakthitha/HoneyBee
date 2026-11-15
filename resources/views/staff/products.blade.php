@extends('layouts.staff')
@section('title', $type ? $sections[$type]['title'].' Products' : 'Products')

@push('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
@endpush

@section('content')
@foreach($sections as $sectionType => $section)
    <div class="page-head">
        <h1>{{ $type ? 'View' : 'Manage' }} <span>{{ $section['title'] }}</span></h1>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-dark" href="{{ route('staff.products.export') }}"><i class="fa-solid fa-file-excel me-1"></i> Export Products</a>
            <a class="btn btn-honey" href="{{ route('staff.products.create', $sectionType) }}"><i class="fa-solid fa-plus me-1"></i> Add Product</a>
        </div>
    </div>

    <section class="honey-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
            <h2 class="h4 mb-0">{{ $section['title'] }}</h2>
            @if($section['items']->isNotEmpty())
                <label class="form-check mb-0"><input class="form-check-input select-all-products" type="checkbox"> <span class="form-check-label">Select all shown</span></label>
            @endif
        </div>
        @if($section['items']->isEmpty())
            <p class="text-muted mb-0">No {{ strtolower($section['title']) }} items yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover admin-datatable staff-product-table align-middle" style="width:100%;">
                    <thead><tr><th>Select</th><th>Product</th><th>Category</th><th>Original Price</th><th>Current Offer</th><th>Created</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($section['items'] as $item)
                        <tr>
                            <td><input class="form-check-input product-checkbox" type="checkbox" aria-label="Select {{ $item->{$section['name']} }}"></td>
                            <td class="text-break"><strong>{{ $item->{$section['name']} }}</strong><small class="d-block text-muted text-break">{{ $item->description ?: 'No description' }}</small></td>
                            <td>{{ $item->{$section['category']} ?: '—' }}</td>
                            <td>Rs. {{ number_format((float) $item->price, 2) }}</td>
                            <td>{{ $item->offer_price !== null ? 'Rs. '.number_format((float) $item->offer_price, 2) : '—' }}</td>
                            <td>{{ $item->created_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('staff.products.show', [$sectionType, $item->{$section['key']}]) }}" aria-label="View details for {{ $item->{$section['name']} }}"><i class="fa-solid fa-eye"></i><span class="visually-hidden"> View</span></a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('staff.products.edit', [$sectionType, $item->{$section['key']}]) }}" aria-label="Edit {{ $item->{$section['name']} }}"><i class="fa-solid fa-pen-to-square"></i><span class="visually-hidden"> Edit</span></a>
                                    <form method="POST" action="{{ route('staff.products.destroy', [$sectionType, $item->{$section['key']}]) }}" onsubmit="return confirm('Are you sure you want to delete this item?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $item->{$section['name']} }}"><i class="fa-solid fa-trash"></i><span class="visually-hidden"> Delete</span></button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endforeach
@endsection

@push('scripts')
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table.staff-product-table').forEach(function (table) {
        const dataTable = new DataTable(table, { pageLength: 10, lengthMenu: [5, 10, 25, 50], order: [] });
        const selectAll = table.closest('.honey-card')?.querySelector('.select-all-products');
        selectAll?.addEventListener('change', function () {
            dataTable.rows({ search: 'applied' }).nodes().to$().find('.product-checkbox').prop('checked', this.checked);
        });
    });
});
</script>
@endpush
