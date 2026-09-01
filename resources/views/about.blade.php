@extends('layouts.app')

@section('title', 'About Us')

@section('content')

    <!-- HERO -->
    <section style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 80px 40px; text-align: center;">
        <h1 style="font-size: 40px; margin-bottom: 15px;">About <span style="color: #f5a623;">HoneyBee</span></h1>
        <p style="color: #ccc; font-size: 16px; max-width: 600px; margin: 0 auto;">We are a creative shop offering unique gifts, precision laser work and unforgettable events!</p>
    </section>

    <!-- ABOUT SECTION -->
    <section style="padding: 60px 40px; max-width: 1100px; margin: 0 auto;">

        <div style="display: flex; gap: 40px; flex-wrap: wrap; align-items: center; margin-bottom: 60px;">
            <div style="flex: 1; min-width: 300px;">
                <img src="{{ asset('images/logo.png') }}" alt="HoneyBee" style="width: 100%; max-width: 400px; border-radius: 15px;">
            </div>
            <div style="flex: 1; min-width: 300px;">
                <h2 style="font-size: 32px; margin-bottom: 20px;">Who <span style="color: #f5a623;">We Are</span></h2>
                <p style="color: #777; font-size: 16px; line-height: 1.8; margin-bottom: 15px;">
                    HoneyBee Shop is a creative business based in Sri Lanka, dedicated to bringing joy and creativity to every occasion. We specialize in three main areas — Gifts & Design, Laser Work, and Events.
                </p>
                <p style="color: #777; font-size: 16px; line-height: 1.8;">
                    Our mission is to provide high quality, personalized products and services that make every moment special and memorable for our customers.
                </p>
            </div>
        </div>

        <!-- WHAT WE DO -->
        <h2 style="text-align: center; font-size: 32px; margin-bottom: 40px;">What We <span style="color: #f5a623;">Do</span></h2>

        <div style="display: flex; gap: 30px; flex-wrap: wrap; justify-content: center; margin-bottom: 60px;">

            <div style="background: #fff; border-radius: 15px; padding: 40px 30px; text-align: center; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <div style="font-size: 48px; margin-bottom: 20px;">🎁</div>
                <h3 style="font-size: 20px; margin-bottom: 10px; color: #f5a623;">Gift & Design</h3>
                <p style="color: #777; line-height: 1.6;">Custom gifts and creative designs for every occasion. We make your special moments unforgettable!</p>
            </div>

            <div style="background: #fff; border-radius: 15px; padding: 40px 30px; text-align: center; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <div style="font-size: 48px; margin-bottom: 20px;">⚡</div>
                <h3 style="font-size: 20px; margin-bottom: 10px; color: #f5a623;">Laser Work</h3>
                <p style="color: #777; line-height: 1.6;">Precision laser engraving and cutting services on wood, acrylic, leather and more!</p>
            </div>

            <div style="background: #fff; border-radius: 15px; padding: 40px 30px; text-align: center; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <div style="font-size: 48px; margin-bottom: 20px;">🎉</div>
                <h3 style="font-size: 20px; margin-bottom: 10px; color: #f5a623;">Events</h3>
                <p style="color: #777; line-height: 1.6;">Full event planning and management services — from birthdays to weddings and corporate events!</p>
            </div>

        </div>

        <!-- WHY CHOOSE US -->
        <div style="background: #1a1a1a; border-radius: 15px; padding: 60px 40px; text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 32px; margin-bottom: 40px; color: #fff;">Why Choose <span style="color: #f5a623;">Us?</span></h2>
            <div style="display: flex; gap: 30px; flex-wrap: wrap; justify-content: center;">

                <div style="width: 200px;">
                    <div style="font-size: 40px; margin-bottom: 15px;">✅</div>
                    <h3 style="color: #f5a623; margin-bottom: 10px;">Quality</h3>
                    <p style="color: #aaa; font-size: 14px;">We ensure top quality in every product and service we deliver</p>
                </div>

                <div style="width: 200px;">
                    <div style="font-size: 40px; margin-bottom: 15px;">💝</div>
                    <h3 style="color: #f5a623; margin-bottom: 10px;">Personalized</h3>
                    <p style="color: #aaa; font-size: 14px;">Every order is customized to match your unique needs and preferences</p>
                </div>

                <div style="width: 200px;">
                    <div style="font-size: 40px; margin-bottom: 15px;">⚡</div>
                    <h3 style="color: #f5a623; margin-bottom: 10px;">Fast Delivery</h3>
                    <p style="color: #aaa; font-size: 14px;">We deliver your orders on time every time without compromise</p>
                </div>

                <div style="width: 200px;">
                    <div style="font-size: 40px; margin-bottom: 15px;">💬</div>
                    <h3 style="color: #f5a623; margin-bottom: 10px;">Support</h3>
                    <p style="color: #aaa; font-size: 14px;">Our friendly team is always ready to help you via WhatsApp</p>
                </div>

            </div>
        </div>

        <!-- CTA -->
        <div style="text-align: center;">
            <h2 style="font-size: 28px; margin-bottom: 20px;">Ready to get started?</h2>
            <a href="/contact" style="background: #f5a623; color: #1a1a1a; padding: 15px 40px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px; margin-right: 15px;">Contact Us</a>
            <a href="https://wa.me/94717714267" target="_blank" style="background: #25D366; color: #fff; padding: 15px 40px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px;">WhatsApp Us</a>
        </div>

    </section>

@endsection
