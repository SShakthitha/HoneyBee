@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')

    <!-- HERO -->
    <section class="content-page contact-page" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 60px 40px; text-align: center;">
        <h1 style="font-size: 40px; margin-bottom: 15px;">📞 Contact <span style="color: #f5a623;">Us</span></h1>
        <p style="color: #ccc; font-size: 16px;">We'd love to hear from you!</p>
    </section>

    <section class="content-page contact-page" style="padding: 60px 40px; max-width: 1100px; margin: 0 auto;">

        <div style="display: flex; gap: 40px; flex-wrap: wrap;">

            <!-- CONTACT FORM -->
            <div style="flex: 1; min-width: 300px; background: #fff; border-radius: 15px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                <h2 style="font-size: 24px; margin-bottom: 25px;">Send us a <span style="color: #f5a623;">Message</span></h2>

                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="/contact" method="POST">
                    @csrf

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Your Name *</label>
                        <input type="text" name="name" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                        @error('name')
                            <p style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Your Email *</label>
                        <input type="email" name="email" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                        @error('email')
                            <p style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Phone Number</label>
                        <input type="text" name="phone"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">Message *</label>
                        <textarea name="message" rows="5" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px;"
                            placeholder="How can we help you?"></textarea>
                        @error('message')
                            <p style="color: red; font-size: 13px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        style="background: #25D366; color: #fff; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; width: 100%;">
                        🟢 Send via WhatsApp
                    </button>

                </form>
            </div>

            <!-- CONTACT INFO -->
            <div style="flex: 1; min-width: 300px;">

                <div style="background: #fff; border-radius: 15px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
                    <h2 style="font-size: 24px; margin-bottom: 25px;">Get in <span style="color: #f5a623;">Touch</span></h2>

                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 30px;">📱</div>
                        <div>
                            <p style="font-weight: bold; margin-bottom: 5px;">WhatsApp</p>
                            <a href="https://wa.me/94717714267" target="_blank" style="color: #25D366; text-decoration: none;">+94 717 714 267</a>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 30px;">📧</div>
                        <div>
                            <p style="font-weight: bold; margin-bottom: 5px;">Email</p>
                            <p style="color: #777;">honeybee@gmail.com</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 30px;">📍</div>
                        <div>
                            <p style="font-weight: bold; margin-bottom: 5px;">Location</p>
                            <p style="color: #777;">Sri Lanka</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 30px;">🕐</div>
                        <div>
                            <p style="font-weight: bold; margin-bottom: 5px;">Working Hours</p>
                            <p style="color: #777;">Mon - Sat: 9AM - 6PM</p>
                        </div>
                    </div>
                </div>

                <!-- WHATSAPP DIRECT -->
                <div style="background: #25D366; border-radius: 15px; padding: 30px; text-align: center;">
                    <p style="color: #fff; font-size: 18px; font-weight: bold; margin-bottom: 15px;">Chat with us directly!</p>
                    <a href="https://wa.me/94717714267" target="_blank"
                        style="background: #fff; color: #25D366; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 16px;">
                        Open WhatsApp
                    </a>
                </div>

            </div>

        </div>

    </section>

@endsection
