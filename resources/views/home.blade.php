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

    <section style="padding: 60px 40px; background: #fffaf0;">
        <h2 style="text-align: center; font-size: 32px; margin-bottom: 10px;">Our <span style="color: #f5a623;">Gallery</span></h2>
        <p style="text-align: center; color: #777; margin-bottom: 40px;">A selection of our latest creations</p>

        @if($galleryImages->isNotEmpty())
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto;">
                @foreach($galleryImages as $galleryImage)
                    <figure style="margin: 0; overflow: hidden; border-radius: 15px; background: #fff; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                        <img src="{{ asset('storage/' . $galleryImage->image) }}" alt="{{ $galleryImage->title ?? $galleryImage->category . ' gallery image' }}" style="display: block; width: 100%; height: 220px; object-fit: cover;">
                        @if($galleryImage->title)
                            <figcaption style="padding: 14px 16px; color: #333; font-weight: 600;">{{ $galleryImage->title }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #777;">Our latest work will be added here soon.</p>
        @endif
    </section>

@endsection
