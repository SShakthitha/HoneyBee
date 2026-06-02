<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::take(6)->get();
        return view('home', compact('services'));
    }
}