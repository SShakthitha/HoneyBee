@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')

<style>
    .dashboard-wrapper {
        padding: 50px 20px;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: auto;
    }

    .welcome-card {
        background: linear-gradient(135deg, #1A1A1A, #292929);
        color: white;
        padding: 35px;
        border-radius: 18px;
        margin-bottom: 30px;
        border-left: 5px solid #F5A623;
    }

    .welcome-card h1 {
        font-family: 'Playfair Display', serif;
        color: #F5A623;
        margin-bottom: 8px;
    }

    .welcome-card p {
        margin: 0;
        color: rgba(255,255,255,.75);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,.07);
        text-align: center;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: #FFF3D6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #F5A623;
        font-size: 22px;
    }

    .stat-card h3 {
        font-size: 28px;
        color: #1A1A1A;
        margin-bottom: 5px;
    }

    .stat-card p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }

    .dashboard-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,.07);
        margin-bottom: 30px;
    }

    .dashboard-section h2 {
        font-family: 'Playfair Display', serif;
        color: #1A1A1A;
        margin-bottom: 20px;
    }

    .action-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .action-card {
        border: 1px solid #eee;
        padding: 22px;
        border-radius: 12px;
        transition: .25s;
    }

    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,.08);
        border-color: #F5A623;
    }

    .action-card i {
        font-size: 28px;
        color: #F5A623;
        margin-bottom: 10px;
    }

    .action-card h4 {
        color: #1A1A1A;
        margin-bottom: 6px;
    }

    .action-card p {
        color: #777;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .dashboard-btn {
        display: inline-block;
        background: #F5A623;
        color: white;
        padding: 8px 18px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .dashboard-btn:hover {
        background: #FFD166;
        color: #1A1A1A;
    }

    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .action-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 500px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .welcome-card {
            padding: 25px;
        }

        .dashboard-section {
            padding: 20px;
        }
    }
</style>

<div class="dashboard-wrapper">

    <div class="dashboard-container">

        {{-- Welcome --}}
        <div class="welcome-card">
            <h1>Welcome back, {{ $customer->full_name }}!</h1>
            <p>
                Welcome to your HoneyBee Shop dashboard.
                Manage your orders and profile from here.
            </p>
        </div>

        {{-- Statistics --}}
        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-bag-check"></i>
                </div>

                <h3>{{ $totalOrders }}</h3>
                <p>Total Orders</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <h3>{{ $pendingOrders }}</h3>
                <p>Pending Orders</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>

                <h3>{{ $completedOrders }}</h3>
                <p>Completed Orders</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-wallet2"></i>
                </div>

                <h3>Rs. {{ number_format($totalSpent, 2) }}</h3>
                <p>Total Spent</p>
            </div>

        </div>

        {{-- Quick Actions --}}
        <div class="dashboard-section">

            <h2>Quick Actions</h2>

            <div class="action-grid">

                <div class="action-card">
                    <i class="bi bi-bag"></i>

                    <h4>My Orders</h4>

                    <p>
                        View your previous and current orders.
                    </p>

                    <a href="{{ route('orders.index') }}" class="dashboard-btn">
                        View Orders
                    </a>
                </div>

                <div class="action-card">
                    <i class="bi bi-person"></i>

                    <h4>My Profile</h4>

                    <p>
                        Update your name, phone number and address.
                    </p>

                    <a href="{{ route('customer.profile.edit') }}" class="dashboard-btn">
                        Edit Profile
                    </a>
                </div>

                <div class="action-card">
                    <i class="bi bi-shop"></i>

                    <h4>Continue Shopping</h4>

                    <p>
                        Explore our gifts, laser work and events.
                    </p>

                    <a href="{{ route('home') }}" class="dashboard-btn">
                        Shop Now
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection

