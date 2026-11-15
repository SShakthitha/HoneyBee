@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<style>
    .order-details-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 50px 20px;
    }

    .order-header {
        background: linear-gradient(135deg, #1A1A1A, #34302a);
        border-left: 6px solid #F5A623;
        border-radius: 18px;
        color: white;
        padding: 30px;
        margin-bottom: 25px;
    }

    .order-header h1 {
        color: #FFD166;
        margin: 0 0 8px;
        font-family: 'Playfair Display', serif;
    }

    .order-header p {
        margin: 0;
        color: rgba(255,255,255,.75);
    }

    .order-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,.08);
        margin-bottom: 25px;
    }

    .order-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    .info-box {
        background: #FFFDF5;
        border: 1px solid #f0ede6;
        padding: 15px;
        border-radius: 10px;
    }

    .info-box strong {
        display: block;
        color: #777;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .info-box span {
        font-weight: 600;
        color: #1A1A1A;
    }

    .items-title {
        font-family: 'Playfair Display', serif;
        margin-bottom: 15px;
        color: #1A1A1A;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table th {
        background: #FFFDF5;
        padding: 13px;
        text-align: left;
        color: #4A4A4A;
        border-bottom: 1px solid #eee;
    }

    .items-table td {
        padding: 15px 13px;
        border-bottom: 1px solid #eee;
    }

    .item-name {
        font-weight: 600;
        color: #1A1A1A;
    }

    .price {
        font-weight: 600;
        white-space: nowrap;
    }

    .total-row {
        text-align: right;
        font-size: 18px;
        font-weight: bold;
        padding-top: 20px;
        color: #1A1A1A;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        text-transform: capitalize;
        background: #fff3cd;
        color: #856404;
    }

    .status.processing {
        background: #cfe2ff;
        color: #084298;
    }

    .status.completed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status.cancelled {
        background: #f8d7da;
        color: #842029;
    }

    .back-button {
        display: inline-block;
        background: #F5A623;
        color: #1A1A1A;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 700;
    }

    .back-button:hover {
        background: #FFD166;
        color: #1A1A1A;
    }

    @media(max-width: 700px) {
        .order-info {
            grid-template-columns: 1fr;
        }

        .items-table {
            min-width: 650px;
        }

        .items-wrapper {
            overflow-x: auto;
        }
    }
</style>

<div class="order-details-page">

    {{-- Header --}}
    <div class="order-header">
        <h1>Order #{{ $order->order_id }}</h1>
        <p>Here are the details of your HoneyBee order.</p>
    </div>

    {{-- Order Information --}}
    <div class="order-card">

        <div class="order-info">

            <div class="info-box">
                <strong>Order Date</strong>
                <span>
                    {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}
                </span>
            </div>

            <div class="info-box">
                <strong>Status</strong>

                <span class="status {{ strtolower($order->status) }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="info-box">
                <strong>Payment Method</strong>
                <span>
                    {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                </span>
            </div>

            <div class="info-box">
                <strong>Delivery Date</strong>
                <span>
                    @if($order->delivery_date)
                        {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}
                    @else
                        Not set
                    @endif
                </span>
            </div>

        </div>

        {{-- Items --}}
        <h2 class="items-title">Order Items</h2>

        @if($order->items->count())

            <div class="items-wrapper">

                <table class="items-table">

                    <thead>
                        <tr>
                            <th>Product / Service</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($order->items as $item)

                            <tr>

                                <td>
                                    <span class="item-name">
                                        {{ $item->item_name }}
                                    </span>
                                </td>

                                <td class="price">
                                    Rs. {{ number_format($item->price, 2) }}
                                </td>

                                <td>
                                    {{ $item->quantity }}
                                </td>

                                <td class="price">
                                    Rs. {{ number_format($item->subtotal, 2) }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

                <div class="total-row">
                    Total:
                    Rs.
                    {{ number_format($order->items->sum('subtotal'), 2) }}
                </div>

            </div>

        @else

            <p style="color:#777;">
                No items found for this order.
            </p>

        @endif

    </div>

    {{-- Notes --}}
    @if($order->attribute)

        <div class="order-card">

            <h2 class="items-title">Special Requirements</h2>

            <p style="color:#555; margin:0;">
                {{ $order->attribute }}
            </p>

        </div>

    @endif

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="{{ route('orders.index') }}" class="back-button">
            ← Back to My Orders
        </a>
        <a href="{{ route('orders.pdf', $order->order_id) }}" class="back-button" style="background:#1A1A1A; color:#fff;">
            Download PDF
        </a>
    </div>

</div>

@endsection
