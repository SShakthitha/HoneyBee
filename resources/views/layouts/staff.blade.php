<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HoneyBee Staff — @yield('title')</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --honey:#f5a623; --dark:#1a1a1a; --cream:#fff8ea; --border:#eee5d5; }
        * { box-sizing:border-box } body { margin:0; font-family:'Segoe UI',sans-serif; color:var(--dark); background:var(--cream); }
        .staff-sidebar { position:fixed; inset:0 auto 0 0; width:250px; padding:28px 18px; background:var(--dark); z-index:10; }
        .brand { display:flex; justify-content:center; margin-bottom:32px }.brand img { height:58px; max-width:100%; object-fit:contain }
        .staff-sidebar a { display:flex; align-items:center; gap:11px; padding:11px 14px; margin:5px 0; border-radius:9px; text-decoration:none; color:#bdbdbd; font-size:15px; }
        .staff-sidebar a:hover { color:var(--honey); background:#303030 }.staff-sidebar a.active { color:var(--dark); background:var(--honey); font-weight:700 }.staff-sidebar .logout { color:var(--honey); margin-top:30px; border:0; background:transparent; width:100%; text-align:left }
        .staff-main { margin-left:250px; padding:36px; min-height:100vh }.page-head { display:flex; align-items:center; justify-content:space-between; gap:18px; margin-bottom:28px }.page-head h1 { font-size:28px; font-weight:700; margin:0 }.page-head h1 span { color:var(--honey) }
        .honey-card { background:#fff; border:1px solid var(--border); box-shadow:0 5px 20px rgba(0,0,0,.07); border-radius:15px; padding:24px }.btn-honey { background:var(--honey); border-color:var(--honey); color:var(--dark); font-weight:700 }.btn-honey:hover { background:#dd941f; border-color:#dd941f; color:var(--dark) }.table thead th { background:var(--honey); color:var(--dark); white-space:nowrap }.badge-status { display:inline-block; padding:.38rem .7rem; border-radius:999px; font-size:.78rem; font-weight:700 }.status-pending { background:#fff3cd; color:#856404 }.status-processing { background:#cfe2ff; color:#084298 }.status-completed { background:#d4edda; color:#155724 }.status-cancelled { background:#f8d7da; color:#842029 }
        .staff-mobile-bar { display:none }
        @media(max-width:900px) { .staff-mobile-bar { display:flex; align-items:center; gap:12px; padding:12px 16px; background:var(--dark); color:#fff; position:sticky; top:0; z-index:30 }.staff-mobile-bar button { border:1px solid rgba(255,255,255,.3); background:transparent; color:#fff; border-radius:7px; padding:7px 10px }.staff-sidebar { transform:translateX(-105%); transition:transform .2s ease; width:min(280px,85vw); overflow-y:auto; box-shadow:8px 0 24px rgba(0,0,0,.25) } body.staff-sidebar-open .staff-sidebar { transform:translateX(0) }.staff-nav-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9 } body.staff-sidebar-open .staff-nav-overlay { display:block }.brand { justify-content:flex-start; margin-bottom:12px }.brand img { height:42px }.staff-main { margin-left:0; padding:22px 16px }.page-head { align-items:flex-start; flex-direction:column }.honey-card { padding:16px } }
    </style><link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
</head>
<body>
<div class="staff-mobile-bar"><button type="button" id="staff-menu-toggle" aria-controls="staff-sidebar" aria-expanded="false"><i class="fa-solid fa-bars"></i> Menu</button><strong>HoneyBee Staff</strong></div><div class="staff-nav-overlay" id="staff-nav-overlay"></div>
<aside class="staff-sidebar" id="staff-sidebar"><div class="brand"><img src="{{ asset('images/logo.png') }}" alt="HoneyBee Shop"></div><nav>
    <a class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a class="{{ request()->routeIs('staff.orders.*') ? 'active' : '' }}" href="{{ route('staff.orders.index') }}"><i class="fa-solid fa-receipt"></i> Orders</a>
    <a class="{{ request()->routeIs('staff.products.*') ? 'active' : '' }}" href="{{ route('staff.products.index') }}"><i class="fa-solid fa-box-open"></i> Products / Business</a>
    <a class="{{ request()->routeIs('staff.gallery.*') ? 'active' : '' }}" href="{{ route('staff.gallery.index') }}"><i class="fa-solid fa-images"></i> Gallery</a>
    <a class="{{ request()->routeIs('staff.promotions.*') ? 'active' : '' }}" href="{{ route('staff.promotions.index') }}"><i class="fa-solid fa-bullhorn"></i> Promotions</a>
    <a class="{{ request()->routeIs('staff.profile*') ? 'active' : '' }}" href="{{ route('staff.profile') }}"><i class="fa-solid fa-user"></i> My Profile</a>
    @if(\App\Models\Staff::where('email', auth()->user()->email)->where('role', 'manager')->exists())
    <a class="{{ request()->routeIs('staff.staff.*') ? 'active' : '' }}" href="{{ route('staff.staff.index') }}"><i class="fa-solid fa-user-tie"></i> Staff Management</a>
    @endif
    <a href="{{ route('home') }}"><i class="fa-solid fa-globe"></i> View Website</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button></form>
</nav></aside>
<main class="staff-main">
@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</main><script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script><script>document.addEventListener('DOMContentLoaded',function(){const t=document.getElementById('staff-menu-toggle'),o=document.getElementById('staff-nav-overlay'),close=()=>{document.body.classList.remove('staff-sidebar-open');t?.setAttribute('aria-expanded','false')};t?.addEventListener('click',()=>{const open=document.body.classList.toggle('staff-sidebar-open');t.setAttribute('aria-expanded',String(open))});o?.addEventListener('click',close);document.querySelectorAll('#staff-sidebar a').forEach(a=>a.addEventListener('click',close));});</script>
</body></html>
