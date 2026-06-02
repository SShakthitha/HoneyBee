@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <!-- HERO SECTION -->
    <section style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 100px 40px; text-align: center;">
        <h1 style="font-size: 48px; margin-bottom: 20px;">
            Welcome to <span style="color: #f5a623;">HoneyBee Shop</span> 🐝
        </h1>
        <p style="font-size: 18px; color: #ccc; margin-bottom: 40px;">
            Your one stop shop for Gift & Design, Laser Work and Events
        </p>
        <a href="/services" style="background-color: #f5a623; color: #1a1a1a; padding: 15px 40px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px;">
            Explore Our Services
        </a>
    </section>

    <!-- SERVICES SECTION -->
    <section style="padding: 60px 40px;">
        <h2 style="text-align: center; font-size: 32px; margin-bottom: 10px;">Our <span style="color: #f5a623;">Services</span></h2>
        <p style="text-align: center; color: #777; margin-bottom: 40px;">What we offer for you</p>

        <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">

            <!-- Gift & Design -->
            <div style="background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 40px 30px; text-align: center; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: transform 0.3s;">
                <div style="font-size: 48px; margin-bottom: 20px;">🎁</div>
                <h3 style="font-size: 20px; margin-bottom: 10px;">Gift & Design</h3>
                <p style="color: #777; margin-bottom: 20px;">Custom gifts and creative designs for every occasion</p>
                <a href="/gift-design" style="background-color: #f5a623; color: #1a1a1a; padding: 10px 25px; border-radius: 25px; text-decoration: none; font-weight: bold;">Explore</a>
            </div>

            <!-- Laser Work -->
            <div style="background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 40px 30px; text-align: center; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <div style="font-size: 48px; margin-bottom: 20px;">⚡</div>
                <h3 style="font-size: 20px; margin-bottom: 10px;">Laser Work</h3>
                <p style="color: #777; margin-bottom: 20px;">Precision laser engraving and cutting services</p>
                <a href="/laser-work" style="background-color: #f5a623; color: #1a1a1a; padding: 10px 25px; border-radius: 25px; text-decoration: none; font-weight: bold;">Explore</a>
            </div>

            <!-- Events -->
            <div style="background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 40px 30px; text-align: center; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <div style="font-size: 48px; margin-bottom: 20px;">🎉</div>
                <h3 style="font-size: 20px; margin-bottom: 10px;">Events</h3>
                <p style="color: #777; margin-bottom: 20px;">Unforgettable event planning and management</p>
                <a href="/events" style="background-color: #f5a623; color: #1a1a1a; padding: 10px 25px; border-radius: 25px; text-decoration: none; font-weight: bold;">Explore</a>
            </div>

        </div>
    </section>

@endsection