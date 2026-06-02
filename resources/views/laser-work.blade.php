@extends('layouts.app')

@section('title', 'Laser Work')

@section('content')

    <!-- HERO -->
    <section style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 60px 40px; text-align: center;">
        <h1 style="font-size: 40px; margin-bottom: 15px;">⚡ Laser <span style="color: #f5a623;">Work</span></h1>
        <p style="color: #ccc; font-size: 16px;">Precision laser engraving and cutting services</p>
    </section>

    <!-- PRODUCTS -->
    <section style="padding: 60px 40px;">

        @if($laserWorks->isEmpty())
            <div style="text-align: center; padding: 60px;">
                <p style="font-size: 20px; color: #777;">No products available yet. Check back soon! 🐝</p>
            </div>
        @else
            <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
                @foreach($laserWorks as $laserWork)
                <div style="background: #fff; border: 1px solid #eee; border-radius: 15px; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); overflow: hidden;">
                    <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 48px;">⚡</div>
                    <div style="padding: 20px;">
                        <h3 style="font-size: 18px; margin-bottom: 8px;">{{ $laserWork->product_name }}</h3>
                        <p style="color: #777; font-size: 14px; margin-bottom: 10px;">{{ $laserWork->product_category }}</p>
                        <p style="color: #f5a623; font-weight: bold; font-size: 18px; margin-bottom: 15px;">Rs. {{ number_format($laserWork->price, 2) }}</p>
                        <a href="/laser-work/{{ $laserWork->laser_id }}" style="background-color: #f5a623; color: #1a1a1a; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px;">View Details</a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </section>

@endsection