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
            @auth
              <li><a href="/orders">My Orders</a></li>
            @endauth
        </ul>
        <div class="nav-buttons">
          @auth
          <a href="/dashboard">{{ Auth::user()->name }}</a>
          <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" style="background-color: #f5a623; color: #1a1a1a; padding: 8px 20px; border-radius: 25px; border: none; font-weight: bold; font-size: 14px; cursor: pointer; margin-left: 10px;">
                Logout
            </button>
          </form>
          @else
            <a href="/login">Login</a>
          @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer style="background-color: #1a1a1a; color: #aaa; padding: 40px; margin-top: 60px;">
      <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px; max-width: 1100px; margin: 0 auto;">
        
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="HoneyBee" style="height: 60px; margin-bottom: 15px;">
            <p style="color: #aaa; font-size: 14px;">Your one stop shop for Gifts,<br>Laser Work and Events 🐝</p>
        </div>

        <div>
            <h3 style="color: #f5a623; margin-bottom: 15px;">Quick Links</h3>
            <ul style="list-style: none;">
                <li style="margin-bottom: 8px;"><a href="/" style="color: #aaa; text-decoration: none;">Home</a></li>
                <li style="margin-bottom: 8px;"><a href="/gift-design" style="color: #aaa; text-decoration: none;">Gift & Design</a></li>
                <li style="margin-bottom: 8px;"><a href="/laser-work" style="color: #aaa; text-decoration: none;">Laser Work</a></li>
                <li style="margin-bottom: 8px;"><a href="/events" style="color: #aaa; text-decoration: none;">Events</a></li>
            </ul>
        </div>

        <div>
            <h3 style="color: #f5a623; margin-bottom: 15px;">Contact Us</h3>
            <ul style="list-style: none;">
              <li style="margin-bottom: 8px;"><a href="/" style="color: #aaa; text-decoration: none;">Home</a></li>
              <li style="margin-bottom: 8px;"><a href="/gift-design" style="color: #aaa; text-decoration: none;">Gift & Design</a></li>
              <li style="margin-bottom: 8px;"><a href="/laser-work" style="color: #aaa; text-decoration: none;">Laser Work</a></li>
              <li style="margin-bottom: 8px;"><a href="/events" style="color: #aaa; text-decoration: none;">Events</a></li>
              <li style="margin-bottom: 8px;"><a href="/about" style="color: #aaa; text-decoration: none;">About Us</a></li>
              <li style="margin-bottom: 8px;"><a href="/contact" style="color: #aaa; text-decoration: none;">Contact Us</a></li>
            </ul>
        </div>

      </div>

      <div style="text-align: center; margin-top: 30px; border-top: 1px solid #333; padding-top: 20px;">
        <p>© 2025 <span style="color: #f5a623;">HoneyBee Shop</span> — All Rights Reserved 🐝</p>
      </div>
    </footer>
</body>
</html>