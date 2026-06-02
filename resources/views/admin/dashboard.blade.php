@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<div style="display: flex; min-height: 80vh;">

    <!-- SIDEBAR -->
    <div style="background: #1a1a1a; width: 250px; padding: 30px 20px;">
        <h2 style="color: #f5a623; margin-bottom: 30px; font-size: 18px;">🐝 Admin Panel</h2>
        <ul style="list-style: none;">
            <li style="margin-bottom: 15px;"><a href="/admin" style="color: #fff; text-decoration: none; font-size: 15px;">📊 Dashboard</a></li>
            <li style="margin-bottom: 15px;"><a href="/admin/services" style="color: #aaa; text-decoration: none; font-size: 15px;">🛍️ Services</a></li>
            <li style="margin-bottom: 15px;"><a href="/admin/orders" style="color: #aaa; text-decoration: none; font-size: 15px;">📦 Orders</a></li>
            <li style="margin-bottom: 15px;"><a href="/admin/customers" style="color: #aaa; text-decoration: none; font-size: 15px;">👥 Customers</a></li>
            <li style="margin-bottom: 15px;"><a href="/admin/staff" style="color: #aaa; text-decoration: none; font-size: 15px;">👨‍💼 Staff</a></li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div style="flex: 1; padding: 40px;">
        <h1 style="font-size: 28px; margin-bottom: 30px;">Dashboard <span style="color: #f5a623;">Overview</span></h1>

        <!-- STATS CARDS -->
        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px;">

            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">🛍️</div>
                <h3 style="font-size: 32px; color: #f5a623;">{{ $totalServices }}</h3>
                <p style="color: #777;">Total Services</p>
            </div>

            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">📦</div>
                <h3 style="font-size: 32px; color: #f5a623;">{{ $totalOrders }}</h3>
                <p style="color: #777;">Total Orders</p>
            </div>

            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">👥</div>
                <h3 style="font-size: 32px; color: #f5a623;">{{ $totalCustomers }}</h3>
                <p style="color: #777;">Total Customers</p>
            </div>

            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">👨‍💼</div>
                <h3 style="font-size: 32px; color: #f5a623;">{{ $totalStaff }}</h3>
                <p style="color: #777;">Total Staff</p>
            </div>

        </div>

    </div>

</div>

@endsection