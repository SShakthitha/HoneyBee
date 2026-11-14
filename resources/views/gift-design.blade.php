@extends('layouts.app')

@section('title', 'Gift & Design')

@section('content')
@php
    $imageUrl = function ($path) {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return str_contains($path, '/') ? asset('storage/'.$path) : asset('images/gifts/'.$path);
    };
@endphp

<section style="background:linear-gradient(135deg,#1a1a1a,#333);color:#fff;padding:56px 24px;text-align:center;">
    <h1 style="font-size:clamp(32px,5vw,48px);margin-bottom:12px;">Creative Gifts &amp; Designs</h1>
    <p style="color:#ddd;margin:0;">Personalised gifts and frames for your special moments.</p>
    <div style="display:flex;justify-content:center;gap:12px;margin-top:24px;flex-wrap:wrap;">
        <a href="#gifts" style="background:#f5a623;color:#1a1a1a;padding:10px 20px;border-radius:24px;text-decoration:none;">Explore Gifts</a>
        <a href="#frames" style="border:1px solid #fff;color:#fff;padding:10px 20px;border-radius:24px;text-decoration:none;">Explore Frames</a>
    </div>
</section>

<section id="gifts" style="padding:48px 24px;">
    <div style="max-width:1180px;margin:auto;">
        <p style="color:#a76a00;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;">Category</p>
        <h2 style="font-size:34px;margin-bottom:8px;">Our Gift Collection</h2>
        <p style="color:#777;margin-bottom:28px;">Thoughtfully curated gifts for every milestone and memory.</p>
        @if($gifts->isEmpty())
            <p style="text-align:center;color:#777;padding:32px 0;">Gift products will be added here soon.</p>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px;">
                @foreach($gifts as $gift)
                    @php
                        $image = $imageUrl($gift->image);
                        $price = $gift->offer_price && $gift->offer_price > 0 && $gift->offer_price < $gift->price ? $gift->offer_price : $gift->price;
                    @endphp
                    <article style="border:1px solid #eee;border-radius:14px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.07);">
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $gift->item_name }}" style="width:100%;height:240px;object-fit:cover;">
                        @else
                            <div style="height:240px;background:#faf3e6;display:grid;place-items:center;color:#777;">Image coming soon</div>
                        @endif
                        <div style="padding:18px;">
                            <h3 style="margin:0 0 8px;">{{ $gift->item_name }}</h3>
                            <p style="color:#777;margin:0 0 12px;">{{ $gift->description ?: $gift->category }}</p>
                            <p style="font-size:18px;margin:0 0 16px;">
                                @if($price != $gift->price)<del style="color:#888;">Rs. {{ number_format((float) $gift->price, 2) }}</del> @endif
                                <strong style="color:#a76a00;">Rs. {{ number_format((float) $price, 2) }}</strong>
                            </p>
                            <a href="https://wa.me/94717714267?text={{ rawurlencode('Hi! I want to order '.$gift->item_name.' - Rs.'.$price) }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;background:#25D366;color:#fff;padding:9px 16px;border-radius:22px;text-decoration:none;">WhatsApp to order</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section id="frames" style="padding:48px 24px;background:#1a1a1a;color:#fff;">
    <div style="max-width:1180px;margin:auto;">
        <p style="color:#f5a623;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;">Category</p>
        <h2 style="font-size:34px;margin-bottom:8px;">Frame Collection</h2>
        <p style="color:#ccc;margin-bottom:28px;">Quality frames crafted to hold your most precious moments.</p>
        @if($frames->isEmpty())
            <p style="text-align:center;color:#ccc;padding:32px 0;">Frame products will be added here soon.</p>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px;">
                @foreach($frames as $frame)
                    @php
                        $image = $imageUrl($frame->image);
                        $price = $frame->offer_price && $frame->offer_price > 0 && $frame->offer_price < $frame->price ? $frame->offer_price : $frame->price;
                    @endphp
                    <article style="background:#fff;color:#333;border-radius:14px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.18);">
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $frame->item_name }}" style="width:100%;height:240px;object-fit:cover;">
                        @else
                            <div style="height:240px;background:#faf3e6;display:grid;place-items:center;color:#777;">Image coming soon</div>
                        @endif
                        <div style="padding:18px;">
                            <h3 style="margin:0 0 8px;">{{ $frame->item_name }}</h3>
                            <p style="color:#777;margin:0 0 12px;">{{ $frame->description ?: $frame->category }}</p>
                            <p style="font-size:18px;margin:0 0 16px;">
                                @if($price != $frame->price)<del style="color:#888;">Rs. {{ number_format((float) $frame->price, 2) }}</del> @endif
                                <strong style="color:#a76a00;">Rs. {{ number_format((float) $price, 2) }}</strong>
                            </p>
                            <a href="https://wa.me/94717714267?text={{ rawurlencode('Hi! I want to order '.$frame->item_name.' - Rs.'.$price) }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;background:#25D366;color:#fff;padding:9px 16px;border-radius:22px;text-decoration:none;">WhatsApp to order</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section id="gallery" style="padding:48px 24px;">
    <div style="max-width:1180px;margin:auto;">
        <h2 style="font-size:34px;margin-bottom:8px;">Gift Gallery</h2>
        <p style="color:#777;margin-bottom:24px;">A selection of our latest personalised gifts and designs.</p>
        @if($giftGalleryImages->isEmpty())
            <p style="color:#777;">Our latest creations will be added here soon.</p>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                @foreach($giftGalleryImages as $galleryImage)
                    <figure style="margin:0;">
                        <img src="{{ $imageUrl($galleryImage->image) }}" alt="{{ $galleryImage->title ?? 'Gift and Design gallery image' }}" style="width:100%;height:220px;object-fit:cover;border-radius:10px;">
                        @if($galleryImage->title)<figcaption style="padding:8px 2px;">{{ $galleryImage->title }}</figcaption>@endif
                    </figure>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section id="frame-designs" style="padding:48px 24px;background:#f7f7f7;">
    <div style="max-width:1180px;margin:auto;">
        <h2 style="font-size:34px;margin-bottom:8px;">Frame Design Gallery</h2>
        <p style="color:#777;margin-bottom:24px;">Browse our frame designs for inspiration.</p>
        @if($frameDesignImages->isEmpty())
            <p style="color:#777;">Frame design pictures will be added here soon.</p>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                @foreach($frameDesignImages as $galleryImage)
                    <figure style="margin:0;">
                        <img src="{{ $imageUrl($galleryImage->image) }}" alt="{{ $galleryImage->title ?? 'Frame design' }}" style="width:100%;height:220px;object-fit:cover;border-radius:10px;">
                        @if($galleryImage->title)<figcaption style="padding:8px 2px;">{{ $galleryImage->title }}</figcaption>@endif
                    </figure>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
