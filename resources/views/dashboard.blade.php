@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<style>
    .customer-dashboard { background: radial-gradient(circle at top right, rgba(245,166,35,.16), transparent 30%), linear-gradient(135deg, #1A1A1A, #34302a); border: 1px solid rgba(245,166,35,.25); border-radius: 22px; box-shadow: 0 20px 50px rgba(26,26,26,.18); max-width: 1200px; margin: 3rem auto; padding: 2rem 1.5rem; }
    .dashboard-welcome { background: rgba(0,0,0,.28); border-left: 6px solid #F5A623; border-radius: 18px; color: #fff; padding: 2.25rem; margin-bottom: 1.75rem; }
    .dashboard-welcome h1, .dashboard-card h2 { font-family: 'Playfair Display', serif; }
    .dashboard-welcome h1 { color: #FFD166; margin: 0 0 .4rem; font-size: clamp(1.85rem, 4vw, 2.6rem); }
    .dashboard-welcome p { color: rgba(255,255,255,.78); margin: 0; }
    .dashboard-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
    .dashboard-stat, .dashboard-card { background: rgba(0,0,0,.58); border: 1px solid rgba(245,166,35,.22); border-radius: 14px; box-shadow: 0 6px 22px rgba(0,0,0,.18); }
    .dashboard-stat { padding: 1.35rem; border-top: 4px solid #F5A623; }
    .dashboard-stat p { color: rgba(255,255,255,.7); margin: 0; font-size: .9rem; }
    .dashboard-stat strong { color: #FFD166; display: block; font-size: 1.8rem; margin-top: .4rem; }
    .dashboard-content { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    .dashboard-card { overflow: hidden; }
    .dashboard-card-head { padding: 1.4rem 1.5rem; border-bottom: 1px solid rgba(255,209,102,.16); display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
    .dashboard-card h2 { color: #FFD166; font-size: 1.4rem; margin: 0; }
    .dashboard-card-body { padding: 1.5rem; }
    .order-row { display: flex; justify-content: space-between; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid rgba(255,255,255,.1); }
    .order-row:last-child { border-bottom: 0; }
    .order-row strong { color: #fff; }
    .order-meta { color: rgba(255,255,255,.64); font-size: .88rem; }
    .status { background: #fff3cd; border-radius: 999px; color: #856404; display: inline-block; font-size: .78rem; font-weight: 600; padding: .2rem .65rem; text-transform: capitalize; }
    .empty-state { color: rgba(255,255,255,.7); padding: 2rem 1rem; text-align: center; }
    .empty-state .bee { color: #F5A623; font-size: 2.5rem; }
    .dashboard-actions { display: grid; gap: .85rem; }
    .dashboard-action { background: rgba(255,255,255,.06); border: 1px solid rgba(255,209,102,.25); border-radius: 10px; color: #fff; display: block; font-weight: 600; padding: 1rem; transition: .2s ease; }
    .dashboard-action:hover { background: #FFD166; color: #1A1A1A; transform: translateY(-2px); }
    .dashboard-action span { color: rgba(255,255,255,.66); display: block; font-size: .82rem; font-weight: 400; margin-top: .15rem; }
    .dashboard-action:hover span { color: #4A4A4A; }
    .dashboard-link { color: #FFD166; font-weight: 600; font-size: .9rem; }
    @media (max-width: 900px) { .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dashboard-content { grid-template-columns: 1fr; } }
    @media (max-width: 520px) { .customer-dashboard { padding: 2rem 1rem; } .dashboard-stats { grid-template-columns: 1fr; } .dashboard-welcome { padding: 1.6rem; } }
</style>

<div class="customer-dashboard">
    <section class="dashboard-welcome">
        <h1>Welcome back, {{ $customer?->full_name ?? $user->name }}!</h1>
        <p>Manage your HoneyBee orders and discover services made just for you.</p>
    </section>

    <section class="dashboard-stats" aria-label="Order summary">
        <div class="dashboard-stat"><p>Total Orders</p><strong>{{ $totalOrders }}</strong></div>
        <div class="dashboard-stat"><p>Pending Orders</p><strong>{{ $pendingOrders }}</strong></div>
        <div class="dashboard-stat"><p>Completed Orders</p><strong>{{ $completedOrders }}</strong></div>
        <div class="dashboard-stat"><p>Total Spent</p><strong>Rs. {{ number_format($totalSpent, 2) }}</strong></div>
    </section>

    <div class="dashboard-content">
        <section class="dashboard-card">
            <div class="dashboard-card-head">
                <h2>Recent Orders</h2>
                @if ($orders->isNotEmpty())
                    <a class="dashboard-link" href="{{ route('orders.index') }}">View all</a>
                @endif
            </div>
            <div class="dashboard-card-body">
                @forelse ($orders->take(5) as $order)
                    <div class="order-row">
                        <div><strong>Order #{{ $order->order_id }}</strong><div class="order-meta">{{ $order->order_date?->format('d M Y') }}</div></div>
                        <div style="text-align:right;"><strong>Rs. {{ number_format($order->paid_amount, 2) }}</strong><div><span class="status">{{ $order->status }}</span></div></div>
                    </div>
                @empty
                    <div class="empty-state"><div class="bee">📦</div><h3>No Orders Yet</h3><p>You have not placed an order yet. Explore our services to get started.</p><a class="dashboard-link" href="{{ route('services') }}">Browse Services</a></div>
                @endforelse
            </div>
        </section>

        <aside class="dashboard-card">
            <div class="dashboard-card-head"><h2>Quick Actions</h2></div>
            <div class="dashboard-card-body dashboard-actions">
                <a class="dashboard-action" href="{{ route('services') }}">Browse Services<span>Explore what HoneyBee can create.</span></a>
                <a class="dashboard-action" href="{{ route('orders.index') }}">My Orders<span>View your order history.</span></a>
                <a class="dashboard-action" href="{{ route('customer.profile.edit') }}">My Profile<span>Update your customer details.</span></a>
                <a class="dashboard-action" href="{{ route('gift.design') }}">Continue Shopping<span>Discover personalised gifts and designs.</span></a>
            </div>
        </aside>
    </div>
</div>
@endsection
