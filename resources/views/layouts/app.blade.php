<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoneyBee - @yield('title', 'Shop')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --amber:#F5A623; --honey:#FFD166; --jet:#1A1A1A; --cream:#FFFDF5; --slate:#4A4A4A; --white:#FFFFFF; --transition:.25s cubic-bezier(.4,0,.2,1);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;font-size:16px;color:var(--slate);background:var(--cream);line-height:1.7;overflow-x:hidden}
        a{text-decoration:none;color:inherit}
        ul{list-style:none}
        img{display:block;width:100%;height:100%;object-fit:cover}
        .container{max-width:1200px;margin:0 auto;padding:0 1.5rem}
        .btn{display:inline-flex;align-items:center;gap:.45rem;font-family:'Inter',sans-serif;font-weight:600;font-size:.9rem;padding:.7rem 1.5rem;border-radius:50px;border:2px solid transparent;cursor:pointer;transition:all var(--transition);white-space:nowrap}
        .btn-cart{background:var(--amber);color:var(--white);padding:.5rem 1rem;font-size:.85rem;position:relative}
        .btn-cart:hover{background:var(--honey);color:var(--jet)}
        .btn-login{background:transparent;color:var(--honey);border-color:rgba(245,166,35,.65);padding:.5rem 1rem;font-size:.85rem}
        .btn-login:hover{background:var(--amber);color:var(--jet)}
        #header{position:fixed;top:0;left:0;right:0;z-index:1000;background:rgba(26,26,26,.96);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid rgba(245,166,35,.15);transition:box-shadow var(--transition)}
        .header.scrolled{box-shadow:0 2px 20px rgba(125,18,18,.4)}
        .header-inner{display:flex;align-items:center;justify-content:space-between;min-height:82px;gap:24px}
        .brand{display:flex;align-items:center;gap:10px;text-decoration:none}
        .brand-logo{width:80px;height:80px;object-fit:contain}
        .brand-name{font-family:'Playfair Display',serif;font-weight:900;font-size:1.4rem;color:var(--white);letter-spacing:.02em}
        .brand-name .amp{color:var(--amber)}
        .nav-links{display:flex;gap:2rem}
        .nav-links a{color:rgba(255,255,255,.75);font-size:.9rem;font-weight:500;padding:8px 14px;border-radius:6px;transition:all var(--transition);position:relative}
        .nav-links a::after{content:'';position:absolute;bottom:-2px;left:0;right:100%;height:2px;background:var(--amber);transition:right var(--transition)}
        .nav-links a:hover,.nav-links a.active{color:var(--amber)}
        .nav-links a:hover::after,.nav-links a.active::after{right:0}
        .header-actions{display:flex;align-items:center;gap:.6rem}
        .hamburger{display:none;background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--white)}
        main{min-height:100vh;padding-top:82px}
        .footer{background:#121212;color:rgba(255,255,255,.72);padding:3rem 0 2rem;margin-top:2rem}
        @media (max-width:900px){
            .hamburger{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border:1px solid rgba(255,255,255,.18);border-radius:8px;font-size:.75rem;text-transform:uppercase}
            #main-nav{display:none;position:fixed;top:82px;left:0;right:0;background:rgba(26,26,26,.97);padding:16px;z-index:999}
            #main-nav.open{display:block}
            .nav-links{flex-direction:column;gap:4px}
            .nav-links a{display:block;padding:12px 16px}
            .brand-name{font-size:1.15rem}
            .brand-logo{width:56px;height:56px}
        }
    </style>

    @stack('styles')
</head>
<body>
    <header class="header" id="header">
        <div class="header-inner container">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="HoneyBee logo" class="brand-logo">
                <span class="brand-name">HoneyBee <span class="amp">Shop</span></span>
            </a>

            <nav id="main-nav">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ route('gift.design') }}" class="{{ request()->routeIs('gift.*') ? 'active' : '' }}">Gift &amp; Design</a></li>
                    <li><a href="{{ route('laser.work') }}" class="{{ request()->routeIs('laser.*') ? 'active' : '' }}">Laser Work</a></li>
                    <li><a href="{{ route('events') }}" class="{{ request()->routeIs('events') ? 'active' : '' }}">Events</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <button class="btn btn-cart" onclick="toggleCart()" type="button">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="20" r="1"></circle>
                        <circle cx="18" cy="20" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39A2 2 0 0 0 9.64 16H19a2 2 0 0 0 2-1.64L23 6H6"></path>
                    </svg>
                    <span id="cart-count">0</span>
                </button>

                @auth
                    <a class="btn btn-login" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="btn btn-login" href="{{ route('login') }}">Login</a>
                @endauth

                <button class="hamburger" id="hamburger" aria-label="Open menu" type="button">Menu</button>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
                <div>
                    <span class="brand-name" style="font-size:1.8rem;">HoneyBee <span class="amp">Shop</span></span>
                    <p style="margin-top:1rem;">Custom gifts, laser work, and memorable events.</p>
                </div>
                <div>
                    <h5 style="color:#F5A623;margin-bottom:.75rem;">Quick Links</h5>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('gift.design') }}">Gift &amp; Design</a></li>
                        <li><a href="{{ route('laser.work') }}">Laser Work</a></li>
                        <li><a href="{{ route('events') }}">Events</a></li>
                    </ul>
                </div>
                <div>
                    <h5 style="color:#F5A623;margin-bottom:.75rem;">Contact</h5>
                    <ul>
                        <li><a href="{{ route('about') }}">About</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div style="text-align:center;margin-top:2rem;border-top:1px solid rgba(255,255,255,.08);padding-top:1.5rem;font-size:.9rem;">
                <p>&copy; 2025 HoneyBee Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const header = document.getElementById('header');
            window.addEventListener('scroll', () => {
                header?.classList.toggle('scrolled', window.scrollY > 40);
            }, { passive: true });

            const hamburger = document.getElementById('hamburger');
            const nav = document.getElementById('main-nav');
            hamburger?.addEventListener('click', () => {
                nav?.classList.toggle('open');
            });
        });
    </script>
</body>
</html>
