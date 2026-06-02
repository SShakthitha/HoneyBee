<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoneyBee Shop - @yield('title')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fff; color: #333; }
        nav { background-color: #1a1a1a; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        nav ul { list-style: none; display: flex; gap: 30px; }
        nav ul li a { color: #fff; text-decoration: none; font-size: 15px; transition: color 0.3s; }
        nav ul li a:hover { color: #f5a623; }
        nav .nav-buttons a { background-color: #f5a623; color: #1a1a1a; padding: 8px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px; }
        footer { background-color: #1a1a1a; color: #aaa; text-align: center; padding: 30px; margin-top: 60px; }
        footer span { color: #f5a623; }
        main { min-height: 80vh; }
    </style>
</head>
<body>
    <nav>
        <a href="/">
            <img src="{{ asset('images/logo.png') }}" alt="HoneyBee Shop" style="height: 50px; width: auto;">
        </a>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/gift-design">Gift & Design</a></li>
            <li><a href="/laser-work">Laser Work</a></li>
            <li><a href="/events">Events</a></li>
        </ul>
        <div class="nav-buttons">
            @auth
                <a href="/dashboard">{{ Auth::user()->name }}</a>
            @else
                <a href="/login">Login</a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>© 2025 <span>HoneyBee Shop</span> — All Rights Reserved 🐝</p>
    </footer>
</body>
</html>