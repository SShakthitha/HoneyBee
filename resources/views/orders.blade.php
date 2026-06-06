@extends('layouts.app')

@section('title', 'My Orders')

@section('content')

    <section style="padding: 60px 40px;">
        <h1 style="font-size: 32px; margin-bottom: 30px;">My <span style="color: #f5a623;">Orders</span></h1>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div style="text-align: center; padding: 60px;">
                <p style="font-size: 20px; color: #777;">No orders yet! 🐝</p>
                <a href="/" style="background: #f5a623; color: #1a1a1a; padding: 10px 25px; border-radius: 25px; text-decoration: none; font-weight: bold; margin-top: 20px; display: inline-block;">
                    Browse Services
                </a>
            </div>
        @else
            <div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5a623; color: #1a1a1a;">
                            <th style="padding: 12px; text-align: left;">Order ID</th>
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
                            <td style="padding: 12px;">{{ $order->service->service_name ?? 'N/A' }}</td>
                            <td style="padding: 12px;">Rs. {{ number_format($order->paid_amount, 2) }}</td>
                            <td style="padding: 12px;">
                                <span style="background: #f5a623; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td style="padding: 12px;">{{ $order->order_date }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

@endsection