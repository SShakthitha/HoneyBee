@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="topbar">
    <h1>{{ $title === 'Gift & Design' ? 'Gift' : $title }} <span>{{ $title === 'Gift & Design' ? '& Design' : 'Management' }}</span></h1>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-dark" href="{{ route('admin.exports.products') }}"><i class="fa-solid fa-file-excel me-1"></i> Export Products</a>
        <a href="{{ route('admin.' . $type . '.create') }}">+ Add {{ $title === 'Gift & Design' ? 'Gift Item' : rtrim($title, 's') }}</a>
    </div>
</div>

@php
    $key = $type === 'gift' ? 'gift_design_id' : ($type === 'laser' ? 'laser_id' : 'event_id');
    $categoryField = $type === 'gift' ? 'category' : ($type === 'laser' ? 'product_category' : 'event_type');
    $categories = $items->pluck($categoryField)->filter()->unique()->sort()->values();
    $name = $type === 'gift' ? 'item_name' : ($type === 'laser' ? 'product_name' : 'event_name');
@endphp

<form id="bulk-offer-form" method="POST" action="{{ route('admin.bulk-offers.apply') }}">
    @csrf
    <input type="hidden" name="product_type" value="{{ $type }}">
    <div class="admin-card mb-4">
        <div class="admin-card-header"><h2 class="h5 mb-0">Bulk Offer Management</h2></div>
        <div class="admin-card-body">
            <p class="text-muted small mb-3">Offers use the original price, never the previous offer price.</p>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3"><label class="form-label" for="scope">Apply to</label><select class="form-select" id="scope" name="scope"><option value="selected">Selected products</option><option value="category">Entire category</option></select></div>
                <div class="col-12 col-md-3"><label class="form-label" for="category">Category</label><select class="form-select" id="category" name="category" disabled><option value="">Choose a category</option>@foreach($categories as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select></div>
                <div class="col-12 col-md-2"><label class="form-label" for="offer_type">Offer type</label><select class="form-select" id="offer_type" name="offer_type"><option value="percentage">Percentage discount</option><option value="fixed">Fixed discount (Rs.)</option></select></div>
                <div class="col-12 col-md-2"><label class="form-label" for="offer_value">Offer value</label><input class="form-control" id="offer_value" type="number" min="0.01" step="0.01" name="offer_value" required></div>
                <div class="col-12 col-md-2 d-grid gap-2"><button class="btn btn-honey" type="submit" data-action="apply">Apply Offer</button><button class="btn btn-outline-danger" type="submit" formmethod="POST" formaction="{{ route('admin.bulk-offers.remove') }}" formnovalidate data-action="remove">Remove Offer</button></div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center gap-2 flex-wrap"><h2 class="h4 mb-0">{{ $title }}</h2><label class="form-check mb-0"><input class="form-check-input" id="select-all-products" type="checkbox"> <span class="form-check-label">Select all shown</span></label></div>
        <div class="admin-card-body">
            @if($items->isEmpty())
                <p class="text-muted mb-0">No {{ strtolower($title) }} items yet.</p>
            @else
                <div class="table-responsive"><table class="table table-hover admin-datatable align-middle" style="width:100%;"><thead><tr><th>Select</th><th>Product</th><th>Category</th><th>Original Price</th><th>Current Offer</th><th>Created</th><th>Actions</th></tr></thead><tbody>
                @foreach($items as $item)
                    <tr><td><input class="form-check-input product-checkbox" type="checkbox" name="product_ids[]" value="{{ $item->$key }}" aria-label="Select {{ $item->$name }}"></td><td class="text-break"><strong>{{ $item->$name }}</strong><small class="d-block text-muted text-break">{{ $item->description ?: 'No description' }}</small></td><td>{{ $item->$categoryField ?: '—' }}</td><td>Rs. {{ number_format((float) $item->price, 2) }}</td><td>{{ $item->offer_price !== null ? 'Rs. ' . number_format((float) $item->offer_price, 2) : '—' }}</td><td>{{ $item->created_at?->format('d M Y') }}</td><td><div class="action-buttons"><a href="{{ route('admin.' . $type . '.edit', $item->$key) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i><span class="visually-hidden">Edit</span></a><button form="delete-item-{{ $item->$key }}" class="btn btn-sm btn-outline-danger" type="submit"><i class="fa-solid fa-trash"></i><span class="visually-hidden">Delete</span></button></div></td></tr>
                @endforeach
                </tbody></table></div>
            @endif
        </div>
    </div>
</form>
@foreach($items as $item)
    <form id="delete-item-{{ $item->$key }}" action="{{ route('admin.' . $type . '.destroy', $item->$key) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">@csrf @method('DELETE')</form>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bulk-offer-form'), scope = document.getElementById('scope'), category = document.getElementById('category'), selectAll = document.getElementById('select-all-products'), offerValue = document.getElementById('offer_value');
    const checks = () => [...document.querySelectorAll('.product-checkbox')];
    scope.addEventListener('change', () => category.disabled = scope.value !== 'category');
    selectAll?.addEventListener('change', () => checks().forEach(input => input.checked = selectAll.checked));
    form.addEventListener('submit', function (event) {
        const action = event.submitter?.dataset.action;
        let method = form.querySelector('input[name="_method"]');
        if (action === 'remove') { method ??= Object.assign(document.createElement('input'), {type: 'hidden', name: '_method'}); method.value = 'DELETE'; form.append(method); } else { method?.remove(); }
        const count = scope.value === 'category' ? 'every product in this category' : checks().filter(input => input.checked).length + ' selected product(s)';
        if (scope.value === 'selected' && !checks().some(input => input.checked)) { event.preventDefault(); alert('Select at least one product.'); return; }
        if (scope.value === 'category' && !category.value) { event.preventDefault(); alert('Choose a category.'); return; }
        const description = action === 'remove' ? `remove offers from ${count}` : `apply this offer to ${count}`;
        if (!confirm(`You are about to ${description}. Continue?`)) event.preventDefault();
    });
});
</script>
@endpush
