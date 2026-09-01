@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')

<div class="topbar">
    <h1>Order <span>#{{ $order->order_id }}</span></h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <strong class="d-block text-muted small">Customer Name</strong>
                <span>{{ $order->customer?->full_name ?? 'Unknown Customer' }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Customer Email</strong>
                <span>{{ $order->customer?->email ?? '-' }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Customer Phone</strong>
                <span>{{ $order->customer?->phone ?? '-' }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Order Date</strong>
                <span>{{ $order->order_date?->format('d M Y') ?? '-' }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Payment Method</strong>
                <span>{{ $order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : '-' }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Paid Amount</strong>
                <span>Rs {{ number_format((float) $order->paid_amount, 2) }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Advanced Paid</strong>
                <span>Rs {{ number_format((float) $order->advanced_paid, 2) }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Discount</strong>
                <span>Rs {{ number_format((float) $order->discount, 2) }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Status</strong>
                <span>{{ ucfirst($order->status) }}</span>
            </div>
            <div class="col-md-6">
                <strong class="d-block text-muted small">Delivery Date</strong>
                <span>{{ $order->delivery_date?->format('d M Y') ?? 'Not set' }}</span>
            </div>
            <div class="col-12">
                <strong class="d-block text-muted small">Special Requirements</strong>
                <span>{{ $order->attribute ?: '-' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h2 class="h4 mb-3">Order Items</h2>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rs {{ number_format((float) $item->price, 2) }}</td>
                            <td>Rs {{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">No items found for this order.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Order Total</th>
                        <th>Rs {{ number_format((float) $order->items->sum('subtotal'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">&larr; Back to Orders</a>
    <a href="{{ route('admin.orders.pdf', $order->order_id) }}" class="btn btn-honey">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
</div>

@endsection
