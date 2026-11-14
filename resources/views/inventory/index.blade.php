@extends($layout)

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="h3 mb-1">Inventory</h1><p class="text-muted mb-0">Physical stock and event capacity overview.</p></div>
        <form method="GET" action="{{ url()->current() }}" class="d-flex gap-2">
            <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Search products or events">
            <button class="btn btn-outline-secondary">Search</button>
        </form>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="table-responsive bg-white rounded shadow-sm mb-5">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Item</th><th>Physical / Capacity</th><th>Reserved</th><th>Booked</th><th>Available</th><th>State</th>@if($canAdjust)<th>Adjustment</th>@endif</tr></thead>
            <tbody>
            @forelse($items as $item)
                @php($low = $item->threshold > 0 && $item->available > 0 && $item->available <= $item->threshold)
                <tr>
                    <td><strong>{{ $item->name }}</strong><br><small class="text-muted">{{ ucfirst($item->type) }}@if($item->productionType) · {{ str_replace('_', ' ', $item->productionType) }}@endif</small></td>
                    <td>{{ $item->physical }}</td><td>{{ $item->reserved }}</td><td>{{ $item->booked }}</td><td>{{ $item->available }}</td>
                    <td>@if($item->productionType === 'custom_made')<span class="badge bg-info text-dark">Made to order</span>@elseif($item->available === 0)<span class="badge bg-danger">Unavailable</span>@elseif($low)<span class="badge bg-warning text-dark">Low</span>@else<span class="badge bg-success">Available</span>@endif</td>
                    @if($canAdjust)
                    <td style="min-width:300px">
                        <form method="POST" action="{{ route($adjustRoute, ['type' => $item->type, 'id' => $item->id]) }}" class="d-flex flex-column gap-2">
                            @csrf @method('PUT')
                            <select class="form-select form-select-sm" name="action"><option value="set">Set stock/capacity</option><option value="add">Add stock/capacity</option><option value="remove">Reduce stock/capacity</option></select>
                            <input class="form-control form-control-sm" type="number" name="quantity" min="0" max="2147483647" value="{{ $item->physical }}" required aria-label="Quantity or capacity">
                            <input class="form-control form-control-sm" type="number" name="low_stock_threshold" min="0" max="2147483647" placeholder="Low threshold (optional)">
                            @if($item->type === 'laser')<select class="form-select form-select-sm" name="production_type"><option value="">Keep production type</option><option value="ready_made">Ready made</option><option value="custom_made">Custom made</option></select>@endif
                            <input class="form-control form-control-sm" name="note" maxlength="500" placeholder="Adjustment note (optional)">
                            <button class="btn btn-sm btn-primary">Save adjustment</button>
                        </form>
                    </td>
                    @endif
                </tr>
            @empty<tr><td colspan="7" class="text-center text-muted py-4">No inventory items match this search.</td></tr>@endforelse
            </tbody>
        </table>
    </div>

    <h2 class="h4 mb-3">Recent adjustments</h2>
    <div class="table-responsive bg-white rounded shadow-sm"><table class="table table-sm align-middle mb-0">
        <thead><tr><th>Date</th><th>Item</th><th>Change</th><th>Before</th><th>After</th><th>By</th><th>Note</th></tr></thead>
        <tbody>@forelse($history as $entry)<tr><td>{{ $entry->created_at?->format('Y-m-d H:i') }}</td><td>{{ ucfirst($entry->item_type) }} #{{ $entry->item_id }}</td><td>{{ ucfirst(str_replace('_', ' ', $entry->adjustment_type)) }} ({{ $entry->quantity_delta > 0 ? '+' : '' }}{{ $entry->quantity_delta }})</td><td>{{ $entry->quantity_before }}</td><td>{{ $entry->quantity_after }}</td><td>{{ $entry->adjustedBy?->name ?? 'System' }}</td><td>{{ $entry->note ?: '—' }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-3">No manual adjustments recorded.</td></tr>@endforelse</tbody>
    </table></div>
</div>
@endsection
