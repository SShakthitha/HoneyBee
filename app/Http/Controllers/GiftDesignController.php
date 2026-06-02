<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GiftDesign;

class GiftDesignController extends Controller
{
    public function index()
    {
        $gifts = GiftDesign::all();
        return view('gift-design', compact('gifts'));
    }

    public function show($id)
    {
        $gift = GiftDesign::findOrFail($id);
        return view('gift-design-detail', compact('gift'));
    }
}