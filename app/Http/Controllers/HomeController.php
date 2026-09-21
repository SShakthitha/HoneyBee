<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Promotion;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::take(6)->get();
        $galleryImages = GalleryImage::latest()->get();
        $promotions = Promotion::active()->latest()->get();
        $featuredItems = collect([
            [
                'items' => GiftDesign::query()
                    ->orderByRaw('CASE WHEN offer_price IS NULL THEN 1 ELSE 0 END')
                    ->orderByDesc('gift_design_id')
                    ->take(2)
                    ->get(),
                'name' => 'item_name',
                'route' => 'gift.design',
                'type' => 'Gift & Design',
            ],
            [
                'items' => Event::query()
                    ->orderByRaw('CASE WHEN offer_price IS NULL THEN 1 ELSE 0 END')
                    ->orderByDesc('event_id')
                    ->take(2)
                    ->get(),
                'name' => 'event_name',
                'route' => 'events',
                'type' => 'Events',
            ],
            [
                'items' => LaserWork::query()
                    ->orderByRaw('CASE WHEN offer_price IS NULL THEN 1 ELSE 0 END')
                    ->orderByDesc('laser_id')
                    ->take(2)
                    ->get(),
                'name' => 'product_name',
                'route' => 'laser.work',
                'type' => 'Laser Work',
            ],
        ])->flatMap(function (array $group) {
            return $group['items']->map(function ($item) use ($group) {
                return [
                    'name' => $item->{$group['name']},
                    'image' => $item->image,
                    'price' => $item->price,
                    'offer_price' => $item->offer_price,
                    'has_offer_price' => $item->hasValidOfferPrice(),
                    'selling_price' => $item->currentSellingPrice(),
                    'discount_percentage' => $item->discountPercentage(),
                    'route' => $group['route'],
                    'type' => $group['type'],
                ];
            });
        });

        return view('home', compact(
            'services',
            'galleryImages',
            'featuredItems',
            'promotions'
        ));
    }

    public function contact()
    {
        return view('contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        // Redirect to WhatsApp with message
        $message = 'Hi! My name is '.$request->name.'. '.$request->message.' (Email: '.$request->email.')';
        $whatsappUrl = 'https://wa.me/94717714267?text='.urlencode($message);

        return redirect($whatsappUrl);
    }

    public function about()
    {
        return view('about');
    }
}
