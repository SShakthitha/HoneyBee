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
use App\Models\Business;
use Yajra\DataTables\Facades\DataTables;

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

    // ─── Businesses ───────────────────────────────────────────
public function businesses()
{
    return view('admin.businesses.index');
}

public function businessesData()
{
    $businesses = Business::select('business_id', 'business_name', 'contact_email', 'phone', 'description');

    return DataTables::of($businesses)
        ->addColumn('action', function ($b) {
            return '
                <div class="d-flex justify-content-end gap-1">
                    <button type="button"
                        class="btn btn-honey btn-sm editBtn"
                        data-id="' . $b->business_id . '">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button type="button"
                        class="btn btn-outline-danger btn-sm deleteBtn"
                        data-id="' . $b->business_id . '">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
}

public function createBusiness()
{
    return view('admin.businesses.create');
}

public function storeBusiness(Request $request)
{
    $request->validate([
        'business_name' => 'required',
        'contact_email' => 'required|email|unique:businesses,contact_email',
        'phone'         => 'required',
    ]);

    Business::create([
        'business_name' => $request->business_name,
        'contact_email' => $request->contact_email,
        'phone'         => $request->phone,
        'description'   => $request->description,
    ]);

    return redirect()->route('admin.businesses')->with('success', 'Business added successfully!');
}

// AJAX GET — returns JSON to populate the edit modal
public function editBusiness($id)
{
    $business = Business::where('business_id', $id)->firstOrFail();
    return response()->json($business);
}

public function updateBusiness(Request $request, $id)
{
    $business = Business::findOrFail($id);
    $business->update([
        'business_name' => $request->business_name,
        'contact_email' => $request->contact_email,
        'phone'         => $request->phone,
        'description'   => $request->description,
    ]);

    return redirect()->route('admin.businesses')->with('success', 'Business updated successfully!');
}

public function deleteBusiness($id)
{
    Business::where('business_id', $id)->delete();
    return redirect()->back()->with('success', 'Business deleted!');
}

    // ─── Services ─────────────────────────────────────────────
    public function services()
    {
        $gifts      = GiftDesign::all();
        $laserWorks = LaserWork::all();
        $events     = Event::all();
        return view('admin.services', compact('gifts', 'laserWorks', 'events'));
    }

    // ─── Orders ───────────────────────────────────────────────
    public function orders()
    {
        $orders = Order::all();
        return view('admin.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return redirect('/admin/orders')->with('success', 'Order status updated!');
    }

    // ─── Customers ────────────────────────────────────────────
    public function customers(Request $request)
    {
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    public function createCustomer()
    {
        return view('admin.customers.create');
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        Customer::create([
            'full_name'       => $request->full_name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'total_spent'     => 0,
            'registered_date' => now(),
        ]);

        return back()->with('success', 'Customer added successfully.');
    }

    public function editCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email,' . $id . ',customer_id',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        $customer->update([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
        ]);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function deleteCustomer($id)
    {
        Customer::where('customer_id', $id)->delete();
        return back()->with('success', 'Customer deleted.');
    }

    // ─── Staff ────────────────────────────────────────────────
    public function staff(Request $request)
    {
        $query = Staff::query();

        if ($request->search) {
            $query->where('staff_id', $request->search)
                  ->orWhere('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('role', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $staff = $query->get();
        return view('admin.staff', compact('staff'));
    }

    public function createStaff()
    {
        return view('admin.staff.create');
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'role'      => 'required',
            'email'     => 'required|email',
            'phone'     => 'required',
            'hire_date' => 'required|date',
        ]);

        Staff::create([
            'business_id' => 1,
            'full_name'   => $request->full_name,
            'role'        => $request->role,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'hire_date'   => $request->hire_date,
        ]);

        return redirect('/admin/staff')->with('success', 'Staff added successfully!');
    }

    public function editStaff($id)
    {
        $member = Staff::findOrFail($id);
        return view('admin.staff.edit', compact('member'));
    }

    public function updateStaff(Request $request, $id)
    {
        $member = Staff::findOrFail($id);
        $member->update($request->all());
        return redirect('/admin/staff')->with('success', 'Staff updated successfully!');
    }

    public function destroyStaff($id)
    {
        Staff::findOrFail($id)->delete();
        return redirect('/admin/staff')->with('success', 'Staff deleted!');
    }

    // ─── Gift & Design ────────────────────────────────────────
    public function createGift()
    {
        $services = Service::where('service_type', 'gift')->get();
        return view('admin.gift.create', compact('services'));
    }

    public function storeGift(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'category'  => 'required',
            'price'     => 'required|numeric',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/gifts'), $imageName);
        }

        GiftDesign::create([
            'item_name'            => $request->item_name,
            'category'             => $request->category,
            'material'             => $request->material,
            'size'                 => $request->size,
            'price'                => $request->price,
            'customization_option' => $request->customization_option,
            'description'          => $request->description,
            'image'                => $imageName,
        ]);

        return redirect('/admin/services')->with('success', 'Gift item added successfully!');
    }

    public function editGift($id)
    {
        $gift     = GiftDesign::findOrFail($id);
        $services = Service::where('service_type', 'gift')->get();
        return view('admin.gift.edit', compact('gift', 'services'));
    }

    public function updateGift(Request $request, $id)
    {
        $gift      = GiftDesign::findOrFail($id);
        $imageName = $gift->image;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/gifts'), $imageName);
        }

        $gift->update([
            'item_name'            => $request->item_name,
            'category'             => $request->category,
            'material'             => $request->material,
            'size'                 => $request->size,
            'price'                => $request->price,
            'customization_option' => $request->customization_option,
            'description'          => $request->description,
            'image'                => $imageName,
        ]);

        return redirect('/admin/services')->with('success', 'Gift item updated successfully!');
    }

    public function destroyGift($id)
    {
        GiftDesign::findOrFail($id)->delete();
        return redirect('/admin/services')->with('success', 'Gift item deleted!');
    }

    // ─── Laser Work ───────────────────────────────────────────
    public function createLaser()
    {
        $services = Service::where('service_type', 'laser')->get();
        return view('admin.laser.create', compact('services'));
    }

    public function storeLaser(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'laser_type'   => 'required',
            'price'        => 'required|numeric',
        ]);

        LaserWork::create([
            'product_name'     => $request->product_name,
            'laser_type'       => $request->laser_type,
            'material_type'    => $request->material_type,
            'product_category' => $request->product_category,
            'size'             => $request->size,
            'price'            => $request->price,
            'engraving_text'   => $request->engraving_text,
            'description'      => $request->description,
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

    // ─── Events ───────────────────────────────────────────────
    public function createEvent()
    {
        return view('admin.event.create');
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'event_name' => 'required',
            'event_type' => 'required',
            'event_date' => 'required|date',
            'price'      => 'required|numeric',
        ]);

        Event::create([
            'event_name'           => $request->event_name,
            'event_type'           => $request->event_type,
            'decoration_type'      => $request->decoration_type,
            'lighting_service'     => $request->has('lighting_service'),
            'sound_service'        => $request->has('sound_service'),
            'dj_service'           => $request->has('dj_service'),
            'photography_service'  => $request->has('photography_service'),
            'cake_service'         => $request->has('cake_service'),
            'event_date'           => $request->event_date,
            'event_location'       => $request->event_location,
            'price'                => $request->price,
            'description'          => $request->description,
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