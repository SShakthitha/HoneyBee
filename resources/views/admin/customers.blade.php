@extends('layouts.admin')

@section('title', 'Customers')

@section('content')

<div class="topbar">
    <h1>All <span>Customers</span></h1>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    @if($customers->isEmpty())
        <p style="text-align: center; color: #777; padding: 40px;">No customers yet! 🐝</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">ID</th>
                    <th style="padding: 12px; text-align: left;">Name</th>
                    <th style="padding: 12px; text-align: left;">Email</th>
                    <th style="padding: 12px; text-align: left;">Phone</th>
                    <th style="padding: 12px; text-align: left;">Total Spent</th>
                    <th style="padding: 12px; text-align: left;">Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">#{{ $customer->customer_id }}</td>
                    <td style="padding: 12px;">{{ $customer->full_name }}</td>
                    <td style="padding: 12px;">{{ $customer->email }}</td>
                    <td style="padding: 12px;">{{ $customer->phone }}</td>
                    <td style="padding: 12px;">Rs. {{ number_format($customer->total_spent, 2) }}</td>
                    <td style="padding: 12px;">{{ $customer->registered_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection