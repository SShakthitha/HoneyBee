@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<style>
    .order-details-page {
        max-width: 1000px;
        margin: 0 auto;
        padding: 3rem 1.5rem;
    }

    .order-details-header {
        background: linear-gradient(135deg, #1A1A1A, #34302a);
        border-left: 6px solid #F5A623;
        border-radius: 18px;
        color: white;
        padding: 2rem 2.25rem;
        margin-bottom: 1.75rem;
    }

    .order-details-header h1 {
        font-family: 'Playfair Display', serif;
        color: #FFD166;
        margin: 0 0 .5rem;
        font-size: 2.2rem;
    }

    .order-details-header p {
        margin: 0;
        color: rgba(255,255,255,.75);
    }

    .details-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 6px 22px rgba(26,26,26,.08);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .details-card h2 {
        font-family: 'Playfair Display', serif;
        color: #1A1A1A;
        margin-bottom: 1.5rem;
        font-size: 1.4rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-box {
        background: #FFFDF5;
        border-radius: 10px;
        padding: 15px;
    }

    .info-label {
        display: block;
        color: #777;
        font-size: .8rem;
        margin-bottom: 5px;
    }

    .info-value {
        font-weight: 600;
        color: #1A1A1A;
    }

    .items-table-wrapper {
        overflow-x: auto;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table th {
        background: #FFFDF5;
        color: #4A4A4A;
        padding: 12px;
        text-align: left;
        font-size: .85rem;
        border-bottom: 1px solid #eee;
    }

    .items-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #eee;
        color: #333;
    }

    .items-table tr:last-child td {
        border-bottom: none;
    }

    .item-name {
        font-weight: 600;
        color: #1A1A1A;
    }

    .total-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .total-box {
        background: #FFF3D6;
        border-radius: 12px;
        padding: 15px 25px;
        min-width: 250px;
        display: flex;
        justify-content: space-between;
        gap: 30px;
    }

    .total-box span:first-child {
        font-weight: 600;
        color: #4A4A4A;
    }

    .total-box span:last-child {
        font-weight: 700;
        color: #1A1A1A;
        font-size: 1.1rem;
    }

    .notes-box {
        background: #f8f8f8;
        border-left: 4px solid #F5A623;
        padding: 15px;
        border-radius: 8px;
        color: #555;
    }

    .back-button {
        display: inline-block;
        background: #F5A623;
        color: #1A1A1A;
        padding: .7rem 1.4rem;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        transition: .2s ease;
    }

    .back-button:hover {
        background: #FFD166;
        color: #1A1A1A;
        transform: translateY(-2px);
    }

    @media (max-width: 700px) {
        .order-details-page {
            padding: 2rem 1rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .details-card {
            padding: 1.25rem;
        }
    }
</style>

<div class="order-details-page">

    {{-- Header --}}
    <section class="order-details-header">

        <h1>
            Order #{{ $order->order_id }}
        </h1>

        <p>
            Here are the details of your HoneyBee order.
        </p>

    </section>


    {{-- Order Information --}}
    <section class="details-card">

        <h2>Order Information</h2>

        <div class="info-grid">

            <div class="info-box">
                <span class="info-label">Order Date</span>

                <span class="info-value">
                    {{ $order->order_date?->format('d M Y') }}
                </span>
            </div>


            <div class="info-box">
                <span class="info-label">Payment Method</span>

                <span class="info-value">
                    {{ ucfirst($order->payment_method) }}
                </span>
            </div>


            <div class="info-box">
                <span class="info-label">Status</span>

                <span class="info-value">
                    {{ ucfirst($order->status) }}
                </span>
            </div>


            <div class="info-box">
                <span class="info-label">Delivery Date</span>

                <span class="info-value">

                    @if($order->delivery_date)
                        {{ $order->delivery_date->format('d M Y') }}
                    @else
                        Not set
                    @endif

                </span>
            </div>

        </div>

    </section>


    {{-- Ordered Items --}}
    <section class="details-card">

        <h2>Ordered Items</h2>

        <div class="items-table-wrapper">

            <table class="items-table">

                <thead>

                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($order->items as $item)

                        <tr>

                            <td>
                                <span class="item-name">
                                    {{ $item->item_name }}
                                </span>
                            </td>

                            <td>
                                {{ $item->quantity }}
                            </td>

                            <td>
                                Rs. {{ number_format($item->price, 2) }}
                            </td>

                            <td>
                                <strong>
                                    Rs. {{ number_format($item->subtotal, 2) }}
                                </strong>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" style="text-align:center; color:#777;">
                                No items found for this order.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Total --}}
        <div class="total-row">

            <div class="total-box">

                <span>Total</span>

                <span>
                    Rs. {{ number_format($order->paid_amount, 2) }}
                </span>

            </div>

        </div>

    </section>


    {{-- Special Requirements --}}
    @if($order->attribute)

        <section class="details-card">

            <h2>Special Requirements</h2>

            <div class="notes-box">
                {{ $order->attribute }}
            </div>

        </section>

    @endif


    {{-- Back --}}
    <a href="{{ route('orders.index') }}" class="back-button">
        ← Back to My Orders
    </a>

</div>

@endsection
