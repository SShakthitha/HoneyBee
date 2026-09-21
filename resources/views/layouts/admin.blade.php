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

        .sidebar-services summary {
            color: #aaa;
            cursor: pointer;
            font-size: 15px;
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            list-style: none;
        }

        .sidebar-services summary::-webkit-details-marker { display: none; }
        .sidebar-services summary:hover,
        .sidebar-services[open] summary { background: #333; color: var(--admin-accent); }
        .sidebar-services summary .caret { margin-left: auto; transition: transform .2s ease; }
        .sidebar-services[open] summary .caret { transform: rotate(180deg); }
        .sidebar-services ul { margin: 5px 0 0; padding-left: 14px; }
        .sidebar-services li { margin-bottom: 3px; }
        .sidebar-services a { font-size: 14px; padding: 8px 12px; }

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
            .admin-mobile-bar { display:flex; position:sticky; top:0; z-index:30; align-items:center; gap:12px; padding:12px 16px; background:var(--admin-dark); color:#fff; }
            .admin-mobile-bar button { border:1px solid rgba(255,255,255,.3); background:transparent; color:#fff; border-radius:7px; padding:7px 10px; }
            .sidebar {
                position: fixed;
                width: min(280px, 85vw);
                min-height: 100vh;
                transform: translateX(-105%);
                transition: transform .2s ease;
                overflow-y: auto;
                box-shadow: 8px 0 24px rgba(0,0,0,.25);
            }
            body.admin-sidebar-open .sidebar { transform:translateX(0); }
            .admin-nav-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:15; }
            body.admin-sidebar-open .admin-nav-overlay { display:block; }
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 20px 14px;
            }
            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
            .admin-card-body { padding:16px; }
            .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_length { margin-bottom:10px; }
        }
        @media (min-width: 901px) { .admin-mobile-bar, .admin-nav-overlay { display:none; } }
    </style>
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
</head>
<body>
    <div class="admin-mobile-bar"><button type="button" id="admin-menu-toggle" aria-controls="admin-sidebar" aria-expanded="false"><i class="fa-solid fa-bars"></i> Menu</button><strong>HoneyBee Admin</strong></div>
    <div class="admin-nav-overlay" id="admin-nav-overlay"></div>
    <div class="sidebar" id="admin-sidebar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="HoneyBee">
        </div>
        <ul>
            @php($servicesOpen = request()->is('admin/services*') || request()->is('admin/gift*') || request()->is('admin/laser*') || request()->is('admin/event*'))
            <li><a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.businesses') }}" class="{{ request()->is('admin/businesses*') ? 'active' : '' }}"><i class="fa-solid fa-building"></i> Business</a></li>
            <li>
                <details class="sidebar-services" {{ $servicesOpen ? 'open' : '' }}>
                    <summary><i class="fa-solid fa-box-open"></i> Services <i class="fa-solid fa-chevron-down caret"></i></summary>
                    <ul>
                        <li><a href="{{ route('admin.services.gift') }}" class="{{ request()->is('admin/services/gift-design') || request()->is('admin/gift*') ? 'active' : '' }}"><i class="fa-solid fa-gift"></i> Gift &amp; Design</a></li>
                        <li><a href="{{ route('admin.services.events') }}" class="{{ request()->is('admin/services/events') || request()->is('admin/event*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Events</a></li>
                        <li><a href="{{ route('admin.services.laser') }}" class="{{ request()->is('admin/services/laser-work') || request()->is('admin/laser*') ? 'active' : '' }}"><i class="fa-solid fa-wand-magic-sparkles"></i> Laser Work</a></li>
                    </ul>
                </details>
            </li>
            <li><a href="/admin/orders" class="{{ request()->is('admin/orders') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i> Orders</a></li>
            <li><a href="/admin/customers" class="{{ request()->is('admin/customers*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="{{ route('admin.promotions.index') }}" class="{{ request()->is('admin/promotions*') ? 'active' : '' }}"><i class="fa-solid fa-bullhorn"></i> Promotions</a></li>
            <li><a href="{{ route('admin.settings.whatsapp.edit') }}" class="{{ request()->is('admin/settings/whatsapp') ? 'active' : '' }}"><i class="fa-brands fa-whatsapp"></i> WhatsApp Settings</a></li>
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
            const toggle = document.getElementById('admin-menu-toggle');
            const overlay = document.getElementById('admin-nav-overlay');
            const closeMenu = () => { document.body.classList.remove('admin-sidebar-open'); toggle?.setAttribute('aria-expanded', 'false'); };
            toggle?.addEventListener('click', () => { const open = document.body.classList.toggle('admin-sidebar-open'); toggle.setAttribute('aria-expanded', String(open)); });
            overlay?.addEventListener('click', closeMenu);
            document.querySelectorAll('#admin-sidebar a').forEach(link => link.addEventListener('click', closeMenu));
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
