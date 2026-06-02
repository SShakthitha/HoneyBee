<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoneyBee Admin - @yield('title')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; display: flex; }

        /* SIDEBAR */
        .sidebar {
            background: #1a1a1a;
            width: 250px;
            min-height: 100vh;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            left: 0;
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
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            color: #aaa;
            text-decoration: none;
            font-size: 15px;
            padding: 10px 15px;
            border-radius: 10px;
            display: block;
            transition: all 0.3s;
        }

        .sidebar ul li a:hover {
            background: #333;
            color: #f5a623;
        }

        .sidebar ul li a.active {
            background: #f5a623;
            color: #1a1a1a;
            font-weight: bold;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 250px;
            padding: 40px;
            flex: 1;
            min-height: 100vh;
        }

        /* TOP BAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 24px;
        }

        .topbar h1 span {
            color: #f5a623;
        }

        .topbar a {
            background: #f5a623;
            color: #1a1a1a;
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="HoneyBee">
        </div>
        <ul>
            <li><a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">📊 Dashboard</a></li>
            <li><a href="/admin/services" class="{{ request()->is('admin/services') ? 'active' : '' }}">🛍️ Services</a></li>
            <li><a href="/admin/orders" class="{{ request()->is('admin/orders') ? 'active' : '' }}">📦 Orders</a></li>
            <li><a href="/admin/customers" class="{{ request()->is('admin/customers') ? 'active' : '' }}">👥 Customers</a></li>
            <li><a href="/admin/staff" class="{{ request()->is('admin/staff') ? 'active' : '' }}">👨‍💼 Staff</a></li>
            <li style="margin-top: 30px;"><a href="/" style="color: #f5a623;">🌐 View Website</a></li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        @yield('content')
    </div>

</body>
</html>