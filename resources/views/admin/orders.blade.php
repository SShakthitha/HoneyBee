@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<div class="topbar">
    <h1>All <span>Orders</span></h1>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    @if($orders->isEmpty())
        <p style="text-align: center; color: #777; padding: 40px;">No orders yet! 🐝</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Order ID</th>
                    <th style="padding: 12px; text-align: left;">Customer</th>
                    <th style="padding: 12px; text-align: left;">Service</th>
                    <th style="padding: 12px; text-align: left;">Amount</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                    <th style="padding: 12px; text-align: left;">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">#{{ $order->order_id }}</td>
                    <td style="padding: 12px;">{{ $order->customer->full_name ?? 'N/A' }}</td>
                    <td style="padding: 12px;">{{ $order->service->service_name ?? 'N/A' }}</td>
                    <td style="padding: 12px;">Rs. {{ number_format($order->paid_amount, 2) }}</td>
                    <td style="padding: 12px;">
                      <form action="/admin/orders/{{ $order->order_id }}/status" method="POST">
                        @csrf
                        <select name="status" onchange="this.form.submit()"
                          style="padding: 5px 10px; border-radius: 20px; border: 1px solid #ddd; background: #f5a623; font-weight: bold; cursor: pointer;">
                          <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                          <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                          <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                          <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                      </form>
                    </td>
                    <td style="padding: 12px;">{{ $order->order_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection