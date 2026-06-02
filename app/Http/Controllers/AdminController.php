<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Event;

class AdminController extends Controller
{
    public function index()
    {
        $totalServices = Service::count();
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $totalStaff = Staff::count();
        return view('admin.dashboard', compact(
            'totalServices',
            'totalOrders', 
            'totalCustomers',
            'totalStaff'
        ));
    }

    public function services()
    {
        $gifts = GiftDesign::all();
        $laserWorks = LaserWork::all();
        $events = Event::all();
        return view('admin.services', compact('gifts', 'laserWorks', 'events'));
    }

    public function orders()
    {
        $orders = Order::all();
        return view('admin.orders', compact('orders'));
    }

    public function customers()
    {
        $customers = Customer::all();
        return view('admin.customers', compact('customers'));
    }

    public function staff()
    {
        $staff = Staff::all();
        return view('admin.staff', compact('staff'));
    }

    // Gift & Design
public function createGift()
{
    $services = Service::where('service_type', 'gift')->get();
    return view('admin.gift.create', compact('services'));
}

public function storeGift(Request $request)
{
    $request->validate([
        'service_id' => 'required',
        'item_name' => 'required',
        'category' => 'required',
        'price' => 'required|numeric',
    ]);

    $imageName = null;
    if($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images/gifts'), $imageName);
    }

    GiftDesign::create([
        'service_id' => $request->service_id,
        'item_name' => $request->item_name,
        'category' => $request->category,
        'material' => $request->material,
        'size' => $request->size,
        'price' => $request->price,
        'customization_option' => $request->customization_option,
        'description' => $request->description,
        'image' => $imageName,
    ]);

    return redirect('/admin/services')->with('success', 'Gift item added successfully!');
}

public function editGift($id)
{
    $gift = GiftDesign::findOrFail($id);
    $services = Service::where('service_type', 'gift')->get();
    return view('admin.gift.edit', compact('gift', 'services'));
}

public function updateGift(Request $request, $id)
{
    $gift = GiftDesign::findOrFail($id);

    $imageName = $gift->image;
    if($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images/gifts'), $imageName);
    }

    $gift->update([
        'item_name' => $request->item_name,
        'category' => $request->category,
        'material' => $request->material,
        'size' => $request->size,
        'price' => $request->price,
        'customization_option' => $request->customization_option,
        'description' => $request->description,
        'image' => $imageName,
    ]);

    return redirect('/admin/services')->with('success', 'Gift item updated successfully!');
}

public function destroyGift($id)
{
    GiftDesign::findOrFail($id)->delete();
    return redirect('/admin/services')->with('success', 'Gift item deleted!');
}

// Laser Work
public function createLaser()
{
    $services = Service::where('service_type', 'laser')->get();
    return view('admin.laser.create', compact('services'));
}

public function storeLaser(Request $request)
{
    $request->validate([
        'service_id' => 'required',
        'product_name' => 'required',
        'laser_type' => 'required',
        'price' => 'required|numeric',
    ]);

    LaserWork::create([
        'service_id' => $request->service_id,
        'product_name' => $request->product_name,
        'laser_type' => $request->laser_type,
        'material_type' => $request->material_type,
        'product_category' => $request->product_category,
        'size' => $request->size,
        'price' => $request->price,
        'engraving_text' => $request->engraving_text,
        'description' => $request->description,
    ]);

    return redirect('/admin/services')->with('success', 'Laser work item added successfully!');
}

public function editLaser($id)
{
    $laserWork = LaserWork::findOrFail($id);
    return view('admin.laser.edit', compact('laserWork'));
}

public function updateLaser(Request $request, $id)
{
    $laserWork = LaserWork::findOrFail($id);
    $laserWork->update($request->all());
    return redirect('/admin/services')->with('success', 'Laser work updated successfully!');
}

public function destroyLaser($id)
{
    LaserWork::findOrFail($id)->delete();
    return redirect('/admin/services')->with('success', 'Laser work deleted!');
}

// Events
public function createEvent()
{
    return view('admin.event.create');
}

public function storeEvent(Request $request)
{
    $request->validate([
        'service_id' => 'required',
        'event_name' => 'required',
        'event_type' => 'required',
        'event_date' => 'required|date',
        'price' => 'required|numeric',
    ]);

    Event::create([
        'service_id' => $request->service_id,
        'event_name' => $request->event_name,
        'event_type' => $request->event_type,
        'decoration_type' => $request->decoration_type,
        'lighting_service' => $request->has('lighting_service'),
        'sound_service' => $request->has('sound_service'),
        'dj_service' => $request->has('dj_service'),
        'photography_service' => $request->has('photography_service'),
        'cake_service' => $request->has('cake_service'),
        'event_date' => $request->event_date,
        'event_location' => $request->event_location,
        'price' => $request->price,
        'description' => $request->description,
    ]);

    return redirect('/admin/services')->with('success', 'Event added successfully!');
  }

  public function editEvent($id)
  {
    $event = Event::findOrFail($id);
    return view('admin.event.edit', compact('event'));
  }

  public function updateEvent(Request $request, $id)
  {
    $event = Event::findOrFail($id);
    $event->update($request->all());
    return redirect('/admin/services')->with('success', 'Event updated successfully!');
  }

  public function destroyEvent($id)
  {
    Event::findOrFail($id)->delete();
    return redirect('/admin/services')->with('success', 'Event deleted!');
  }
}