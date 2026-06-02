<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaserWork;

class LaserWorkController extends Controller
{
    public function index()
    {
        $laserWorks = LaserWork::all();
        return view('laser-work', compact('laserWorks'));
    }

    public function show($id)
    {
        $laserWork = LaserWork::findOrFail($id);
        return view('laser-work-detail', compact('laserWork'));
    }
}