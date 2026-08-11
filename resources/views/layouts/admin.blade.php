<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoneyBee Admin - @yield('title')</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --admin-accent: #f5a623;
            --admin-dark: #1a1a1a;
            --admin-border: #e9ecef;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            color: #212529;
            display: flex;
        }

        .sidebar {
            background: var(--admin-dark);
            width: 250px;
            min-height: 100vh;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 20;
        }

        .sidebar .logo {
            margin-bottom: 40px;
            text-align: center;
        }

        .sidebar .logo img {
            height: 60px;
            width: auto;
        }

        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            color: #aaa;
            text-decoration: none;
            font-size: 15px;
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .sidebar ul li a:hover {
            background: #333;
            color: var(--admin-accent);
        }

        .sidebar ul li a.active {
            background: var(--admin-accent);
            color: var(--admin-dark);
            font-weight: bold;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            flex: 1;
            min-height: 100vh;
            max-width: calc(100% - 250px);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 24px;
            margin-bottom: 0;
            font-weight: 700;
        }

        .topbar h1 span {
            color: var(--admin-accent);
        }

        .topbar a {
            background: var(--admin-accent);
            color: var(--admin-dark);
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .admin-card {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        .admin-card-header {
            border-bottom: 1px solid var(--admin-border);
            padding: 18px 22px;
        }

        .admin-card-body {
            padding: 22px;
        }

        .admin-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .btn-honey {
            --bs-btn-bg: var(--admin-accent);
            --bs-btn-border-color: var(--admin-accent);
            --bs-btn-color: var(--admin-dark);
            --bs-btn-hover-bg: #dd941f;
            --bs-btn-hover-border-color: #dd941f;
            --bs-btn-hover-color: var(--admin-dark);
            font-weight: 700;
        }

        .table thead th {
            background: var(--admin-accent);
            color: var(--admin-dark);
            border-bottom: 0;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .action-buttons {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .form-box {
            max-width: 980px;
        }

        .form-label {
            font-weight: 700;
        }

        @media (max-width: 900px) {
            body { display: block; }
            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 24px;
            }
            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="HoneyBee">
        </div>
        <ul>
            <li><a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.businesses') }}" class="{{ request()->is('admin/businesses*') ? 'active' : '' }}"><i class="fa-solid fa-building"></i> Business</a></li>
            <li><a href="/admin/services" class="{{ request()->is('admin/services') || request()->is('admin/gift*') || request()->is('admin/laser*') || request()->is('admin/event*') ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i> Services</a></li>
            <li><a href="/admin/orders" class="{{ request()->is('admin/orders') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i> Orders</a></li>
            <li><a href="/admin/customers" class="{{ request()->is('admin/customers*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="/admin/staff" class="{{ request()->is('admin/staff*') ? 'active' : '' }}"><i class="fa-solid fa-user-tie"></i> Staff</a></li>
            <li>
                <a href="{{ route('admin.gallery.index') }}"
                    class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i>
                    Gallery
                </a>
            </li>
            <li style="margin-top: 30px;"><a href="/" style="color: #f5a623;"><i class="fa-solid fa-globe"></i> View Website</a></li>
        </ul>
    </div>

    <main class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table.admin-datatable').forEach(function (table) {
                new DataTable(table, {
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    order: []
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
