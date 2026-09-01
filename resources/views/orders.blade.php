@extends('layouts.app')

@section('title', 'My Orders')

@section('content')

<style>
    .orders-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1.5rem;
    }

    .orders-header {
        background: linear-gradient(135deg, #1A1A1A, #34302a);
        border-left: 6px solid #F5A623;
        border-radius: 18px;
        color: #fff;
        padding: 2rem 2.25rem;
        margin-bottom: 1.75rem;
    }

    .orders-header h1 {
        font-family: 'Playfair Display', serif;
        color: #FFD166;
        margin: 0 0 .4rem;
        font-size: 2.3rem;
    }

    .orders-header p {
        margin: 0;
        color: rgba(255,255,255,.78);
    }

    .orders-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 6px 22px rgba(26,26,26,.08);
        overflow: hidden;
    }

    .orders-card-header {
        padding: 1.4rem 1.5rem;
        border-bottom: 1px solid #f0ede6;
    }

    .orders-card-header h2 {
        font-family: 'Playfair Display', serif;
        color: #1A1A1A;
        margin: 0;
        font-size: 1.4rem;
    }

    .orders-table-wrapper {
        overflow-x: auto;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
    }

    .orders-table th {
        background: #FFFDF5;
        color: #4A4A4A;
        font-size: .85rem;
        text-align: left;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f0ede6;
        white-space: nowrap;
    }

    .orders-table td {
        padding: 1.1rem 1.25rem;
        color: #333;
        border-bottom: 1px solid #f0ede6;
        vertical-align: middle;
    }

    .orders-table tr:last-child td {
        border-bottom: 0;
    }

    .order-number {
        font-weight: 700;
        color: #1A1A1A;
    }

    .service-name {
        font-weight: 600;
        color: #1A1A1A;
    }

    .order-date {
        color: #777;
        font-size: .9rem;
    }

    .amount {
        font-weight: 700;
        color: #1A1A1A;
        white-space: nowrap;
    }

    .status {
        display: inline-block;
        padding: .3rem .75rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 600;
        text-transform: capitalize;
        background: #fff3cd;
        color: #856404;
    }

    .status.completed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status.cancelled {
        background: #f8d7da;
        color: #842029;
    }

    .status.processing {
        background: #cfe2ff;
        color: #084298;
    }

    .empty-orders {
        text-align: center;
        padding: 4rem 1.5rem;
    }

    .empty-orders .bee {
        font-size: 3.5rem;
        margin-bottom: 1rem;
    }

    .empty-orders h3 {
        color: #1A1A1A;
        margin-bottom: .5rem;
    }

    .empty-orders p {
        color: #777;
        margin-bottom: 1.5rem;
    }

    .browse-button {
        display: inline-block;
        background: #F5A623;
        color: #1A1A1A;
        padding: .75rem 1.5rem;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        transition: .2s ease;
    }

    .browse-button:hover {
        background: #FFD166;
        color: #1A1A1A;
        transform: translateY(-2px);
    }

    @media (max-width: 700px) {
        .orders-page {
            padding: 2rem 1rem;
        }

        .orders-header {
            padding: 1.6rem;
        }

        .orders-header h1 {
            font-size: 1.9rem;
        }
    }
</style>

<div class="orders-page">

    <section class="orders-header">
        <h1>My Orders</h1>
        <p>View and track all your HoneyBee orders.</p>
    </section>

    <section class="orders-card">

        <div class="orders-card-header">
            <h2>Order History</h2>
        </div>

        @if($orders->isNotEmpty())

            <div class="orders-table-wrapper">

                <table class="orders-table">

                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Products / Services</th>
                            <th>Order Date</th>
                            <th>Amount Paid</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Delivery Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($orders as $order)

                            <tr>

                                <td>
                                    <span class="order-number">
                                        #{{ $order->order_id }}
                                    </span>
                                </td>

                                <td>
                                    @if($order->items->isNotEmpty())

                                        @foreach($order->items as $item)

                                            <div style="margin-bottom: 8px;">

                                                <span class="service-name">
                                                    {{ $item->item_name }}
                                                </span>

                                                <br>

                                                <small style="color:#777;">
                                                    Qty: {{ $item->quantity }}
                                                    ×
                                                    Rs. {{ number_format($item->price, 2) }}
                                                </small>

                                            </div>

                                        @endforeach

                                    @else

                                        <span style="color:#999;">
                                            No items
                                        </span>

                                    @endif
                                </td>

                                <td>
                                    <span class="order-date">
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="amount">
                                        Rs. {{ number_format($order->paid_amount, 2) }}
                                    </span>
                                </td>

                                <td>
                                    {{ ucfirst($order->payment_method) }}
                                </td>

                                <td>
                                    <span class="status {{ strtolower($order->status) }}">
                                        {{ $order->status }}
                                    </span>
                                </td>

                                <td>
                                    @if($order->delivery_date)
                                        {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}
                                    @else
                                        <span class="order-date">Not set</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('orders.show', $order->order_id) }}"
                                      class="browse-button"
                                      style="padding: .5rem 1rem; font-size: .85rem;">
                                        View
                                    </a>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty-orders">

                <div class="bee">📦</div>

                <h3>No Orders Yet</h3>

                <p>
                    You haven't placed any orders yet.
                    Explore our services and start your first order.
                </p>

                <a href="{{ route('services') }}" class="browse-button">
                    Browse Services
                </a>

            </div>

        @endif

    </section>

</div>

@endsection
