<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaserWork;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'laser_type' => 'required',
            'price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
        ]);

        $laser = new LaserWork();

        $laser->product_name = $request->product_name;
        $laser->laser_type = $request->laser_type;
        $laser->material_type = $request->material_type;
        $laser->product_category = $request->product_category;
        $laser->size = $request->size;

        $laser->price = $request->price;
        $laser->offer_price = $request->offer_price ?? null;

        $laser->engraving_text = $request->engraving_text;
        $laser->description = $request->description;

        if ($request->hasFile('image')) {
            $laser->image = $request->file('image')->store('laser', 'public');
        }

        $laser->save();

        return redirect('/admin/laser')->with('success', 'Laser product added successfully!');
    }

    public function edit($id)
    {
        $laserWork = LaserWork::findOrFail($id);
        return view('admin.laser.edit', compact('laserWork'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required',
            'laser_type' => 'required',
            'price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
        ]);

        $laser = LaserWork::findOrFail($id);

        $laser->product_name = $request->product_name;
        $laser->laser_type = $request->laser_type;
        $laser->material_type = $request->material_type;
        $laser->product_category = $request->product_category;
        $laser->size = $request->size;

        $laser->price = $request->price;
        $laser->offer_price = $request->offer_price ?? null;

        $laser->engraving_text = $request->engraving_text;
        $laser->description = $request->description;

        if ($request->hasFile('image')) {

            if ($laser->image && Storage::disk('public')->exists($laser->image)) {
                Storage::disk('public')->delete($laser->image);
            }

            $laser->image = $request->file('image')->store('laser', 'public');
        }

        $laser->save();

        return redirect('/admin/laser')->with('success', 'Laser product updated successfully!');
    }
}