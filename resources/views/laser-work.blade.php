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

            <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                ⚡
            </div>

            <div style="padding: 20px;">
                <h3 style="font-size: 18px; margin-bottom: 8px;">{{ $laserWork->product_name }}</h3>
                <p style="color: #777; font-size: 14px; margin-bottom: 10px;">{{ $laserWork->product_category }}</p>

                <!-- PRICE -->
                <p style="font-weight: bold; font-size: 18px; margin-bottom: 15px;">
                    @if($laserWork->offer_price)
                        <del style="color:#999;">
                            Rs. {{ number_format($laserWork->price, 2) }}
                        </del>
                        <br>
                        <span style="color:#f5a623;">
                            Rs. {{ number_format($laserWork->offer_price, 2) }}
                        </span>
                  @else
                        <span style="color:#f5a623;">
                            Rs. {{ number_format($laserWork->price, 2) }}
                        </span>
                  @endif
                </p>


                <!-- WHATSAPP -->
                <a href="https://wa.me/94717714267?text=Hi!%20I%20want%20to%20order%20{{ urlencode($laserWork->product_name) }}%20-%20Rs.{{ $laserWork->offer_price ?? $laserWork->price }}"
                   target="_blank"
                   style="background-color: #25D366; color: #fff; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px;">
                   🟢 Order via WhatsApp
                </a>

            </div>
        </div>
        @endforeach
    </div>
@endif

</section>

@endsection