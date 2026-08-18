@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<div class="topbar">
    <h1>All <span>Orders</span></h1>
</div>

@if(session('success'))
    <div style="
        background:#d4edda;
        color:#155724;
        padding:15px;
        border-radius:10px;
        margin-bottom:20px;
    ">
        {{ session('success') }}
    </div>
@endif

<div style="
    background:#fff;
    border-radius:15px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    overflow-x:auto;
">

    @if($orders->isEmpty())

        <p style="
            text-align:center;
            color:#777;
            padding:40px;
        ">
            No orders yet!
        </p>

    @else

        <table style="
            width:100%;
            border-collapse:collapse;
            min-width:900px;
        ">

            <thead>
                <tr style="background:#f8f8f8;">
                    <th style="padding:14px; text-align:left;">Order ID</th>
                    <th style="padding:14px; text-align:left;">Customer</th>
                    <th style="padding:14px; text-align:left;">Date</th>
                    <th style="padding:14px; text-align:left;">Items</th>
                    <th style="padding:14px; text-align:left;">Total</th>
                    <th style="padding:14px; text-align:left;">Status</th>
                    <th style="padding:14px; text-align:left;">Action</th>
                </tr>
            </thead>

            <tbody>

                @foreach($orders as $order)

                    <tr style="border-bottom:1px solid #eee;">

                        <td style="padding:14px;">
                            #{{ $order->order_id }}
                        </td>

                        <td style="padding:14px;">
                            @if($order->customer)
                                <strong>{{ $order->customer->full_name }}</strong>
                                <br>
                                <small style="color:#777;">
                                    {{ $order->customer->email }}
                                </small>

                                @if($order->customer->phone)
                                    <br>
                                    <small style="color:#777;">
                                        {{ $order->customer->phone }}
                                    </small>
                                @endif
                            @else
                                Unknown Customer
                            @endif
                        </td>

                        <td style="padding:14px;">
                            {{ $order->order_date?->format('Y-m-d') }}
                        </td>

                        <td style="padding:14px;">

                            @if($order->items && $order->items->count())

                                @foreach($order->items as $item)

                                    <div style="margin-bottom:8px;">
                                        <strong>
                                            {{ $item->item_name }}
                                        </strong>

                                        <br>

                                        <small style="color:#777;">
                                            Qty: {{ $item->quantity }}
                                            ×
                                            Rs {{ number_format($item->price, 2) }}
                                        </small>
                                    </div>

                                @endforeach

                            @else

                                <span style="color:#999;">
                                    No items
                                </span>

                            @endif

                        </td>

                        <td style="padding:14px;">
                            <strong>
                                Rs {{ number_format($order->paid_amount, 2) }}
                            </strong>
                        </td>

                        <td style="padding:14px;">

                            @php
                                $statusColors = [
                                    'pending' => '#fff3cd',
                                    'processing' => '#cfe2ff',
                                    'completed' => '#d4edda',
                                    'cancelled' => '#f8d7da',
                                ];

                                $statusTextColors = [
                                    'pending' => '#856404',
                                    'processing' => '#084298',
                                    'completed' => '#155724',
                                    'cancelled' => '#721c24',
                                ];
                            @endphp

                            <span style="
                                display:inline-block;
                                padding:6px 12px;
                                border-radius:20px;
                                background:{{ $statusColors[$order->status] ?? '#eee' }};
                                color:{{ $statusTextColors[$order->status] ?? '#333' }};
                                font-size:13px;
                                font-weight:bold;
                            ">
                                {{ ucfirst($order->status) }}
                            </span>

                        </td>

                        <td style="padding:14px;">

                            <form
                                method="POST"
                                action="{{ route('admin.orders.status', $order->order_id) }}"
                            >

                                @csrf
                                @method('PUT')

                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    style="
                                        padding:8px;
                                        border:1px solid #ddd;
                                        border-radius:8px;
                                    "
                                >

                                    <option value="pending"
                                        {{ $order->status === 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="processing"
                                        {{ $order->status === 'processing' ? 'selected' : '' }}>
                                        Processing
                                    </option>

                                    <option value="completed"
                                        {{ $order->status === 'completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>

                                    <option value="cancelled"
                                        {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                        Cancelled
                                    </option>

                                </select>

                            </form>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif

</div>

@endsection