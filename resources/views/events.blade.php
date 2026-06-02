@extends('layouts.app')

@section('title', 'Events')

@section('content')

    <!-- HERO -->
    <section style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 60px 40px; text-align: center;">
        <h1 style="font-size: 40px; margin-bottom: 15px;">🎉 Our <span style="color: #f5a623;">Events</span></h1>
        <p style="color: #ccc; font-size: 16px;">Unforgettable event planning and management</p>
    </section>

    <!-- EVENTS -->
    <section style="padding: 60px 40px;">

        @if($events->isEmpty())
            <div style="text-align: center; padding: 60px;">
                <p style="font-size: 20px; color: #777;">No events available yet. Check back soon! 🐝</p>
            </div>
        @else
            <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
                @foreach($events as $event)
                <div style="background: #fff; border: 1px solid #eee; border-radius: 15px; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); overflow: hidden;">
                    <div style="width: 100%; height: 200px; background: #fff0f0; display: flex; align-items: center; justify-content: center; font-size: 48px;">🎉</div>
                    <div style="padding: 20px;">
                        <h3 style="font-size: 18px; margin-bottom: 8px;">{{ $event->event_name }}</h3>
                        <p style="color: #777; font-size: 14px; margin-bottom: 5px;">{{ $event->event_type }}</p>
                        <p style="color: #777; font-size: 14px; margin-bottom: 10px;">📅 {{ $event->event_date }}</p>
                        <p style="color: #f5a623; font-weight: bold; font-size: 18px; margin-bottom: 15px;">Rs. {{ number_format($event->price, 2) }}</p>
                        <a href="/events/{{ $event->event_id }}" style="background-color: #f5a623; color: #1a1a1a; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px;">View Details</a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </section>

@endsection