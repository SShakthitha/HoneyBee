<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\GalleryImage;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::take(6)->get();
        $galleryImages = GalleryImage::latest()->get();

        return view('home', compact(
            'services',
            'galleryImages'
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
      $message = "Hi! My name is " . $request->name . ". " . $request->message . " (Email: " . $request->email . ")";
      $whatsappUrl = "https://wa.me/94717714267?text=" . urlencode($message);

      return redirect($whatsappUrl);
    }

    public function about()
    {
      return view('about');
    }
}