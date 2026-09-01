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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function index()
    {
        // Count products/services from each actual service table
        $totalGifts = GiftDesign::count();
        $totalLaserWorks = LaserWork::count();
        $totalEvents = Event::count();

        // Total services = all products from the 3 service categories
        $totalServices = $totalGifts + $totalLaserWorks + $totalEvents;

        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $totalStaff = Staff::count();

        return view('admin.dashboard', compact(
            'totalServices',
            'totalGifts',
            'totalLaserWorks',
            'totalEvents',
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
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', Rule::unique('businesses')->ignore($business->business_id, 'business_id')],
            'phone' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
        ]);
        $business->update($validated);

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
        $orders = Order::with(['customer', 'items'])
            ->latest('order_date')
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['customer', 'items'])
            ->where('order_id', $id)
            ->firstOrFail();

        return view('admin.order-show', compact('order'));
    }

    public function downloadOrderPdf($id)
    {
        $order = Order::with(['customer', 'items'])
            ->where('order_id', $id)
            ->firstOrFail();

        $logoPath = public_path('images/logo.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return Pdf::loadView('pdf.order-summary', compact('order', 'logo'))
            ->setPaper('a4')
            ->download('honeybee-order-' . $order->order_id . '.pdf');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders')->with('success', 'Order status updated!');
    }

    public function ordersData(Request $request)
    {
        $orders = Order::with(['customer', 'items'])
            ->select('orders.*');

        $categoryItemTypes = [
            'gift-design' => ['gift', 'frame'],
            'laser-work' => ['laser'],
            'events' => ['event'],
        ];

        $category = $request->query('category');

        if (isset($categoryItemTypes[$category])) {
            $orders->whereHas('items', function ($query) use ($categoryItemTypes, $category) {
                $query->whereIn('item_type', $categoryItemTypes[$category]);
            });
        }

        return DataTables::of($orders)

            ->addColumn('customer', function ($order) {
                if (!$order->customer) {
                    return '<span class="text-muted">Unknown Customer</span>';
                }

                $html = '<strong>' . e($order->customer->full_name) . '</strong>';

                if ($order->customer->email) {
                    $html .= '<br><small class="text-muted">'
                        . e($order->customer->email)
                        . '</small>';
                }

                if ($order->customer->phone) {
                    $html .= '<br><small class="text-muted">'
                        . e($order->customer->phone)
                        . '</small>';
                }

                return $html;
            })

            ->addColumn('date', function ($order) {
                return $order->order_date
                    ? $order->order_date->format('Y-m-d')
                    : '-';
            })

            ->addColumn('items', function ($order) {
                if (!$order->items || $order->items->isEmpty()) {
                    return '<span class="text-muted">No items</span>';
                }

                $html = '';

                foreach ($order->items as $item) {
                    $html .= '<div class="mb-2">';
                    $html .= '<strong>' . e($item->item_name) . '</strong>';
                    $html .= '<br>';
                    $html .= '<small class="text-muted">';
                    $html .= 'Qty: ' . (int) $item->quantity;
                    $html .= ' × Rs ' . number_format((float) $item->price, 2);
                    $html .= '</small>';
                    $html .= '</div>';
                }

                return $html;
            })

            ->addColumn('total', function ($order) {
                $total = $order->items->sum(function ($item) {
                    return (float) $item->subtotal;
                });

                return '<strong>Rs ' . number_format($total, 2) . '</strong>';
            })

            ->addColumn('status', function ($order) {

                $statusColors = [
                    'pending' => '#fff3cd',
                    'processing' => '#cfe2ff',
                    'completed' => '#d4edda',
                    'cancelled' => '#f8d7da',
                ];

                $statusTextColors = [
                    'pending' => '#856404',
                    'processing' => '#084298',
                    'completed' => '#155724',
                    'cancelled' => '#721c24',
                ];

                $background = $statusColors[$order->status] ?? '#eee';
                $color = $statusTextColors[$order->status] ?? '#333';

                return '
                    <span style="
                        display:inline-block;
                        padding:6px 12px;
                        border-radius:20px;
                        background:' . $background . ';
                        color:' . $color . ';
                        font-size:13px;
                        font-weight:bold;
                    ">
                        ' . ucfirst(e($order->status)) . '
                    </span>
                ';
            })

            ->addColumn('action', function ($order) {

                $statuses = [
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ];

                $viewUrl = route('admin.orders.show', $order->order_id);

                $html = '
                    <div class="d-flex align-items-center gap-2">

                        <a
                            href="' . $viewUrl . '"
                            class="btn btn-sm btn-outline-primary"
                            title="View Order"
                        >
                            <i class="fa-solid fa-eye"></i>
                            View
                        </a>

                        <form
                            method="POST"
                            action="' . route('admin.orders.status', $order->order_id) . '"
                            style="margin:0;"
                        >
                            ' . csrf_field() . '
                            <input type="hidden" name="_method" value="PUT">

                            <select
                                name="status"
                                onchange="this.form.submit()"
                                class="form-select form-select-sm"
                                style="min-width:130px;"
                            >
                ';

                foreach ($statuses as $value => $label) {

                    $selected = $order->status === $value
                        ? 'selected'
                        : '';

                    $html .= '
                        <option value="' . $value . '" ' . $selected . '>
                            ' . $label . '
                        </option>
                    ';
                }

                $html .= '
                            </select>
                        </form>

                    </div>
                ';

                return $html;
            })
            ->rawColumns(['customer', 'items', 'total', 'status', 'action'])

            ->make(true);
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
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Gift,Frame'],
            'price' => ['required', 'numeric', 'min:0'], 'offer_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);
        $imageName = $request->file('image')?->store('gifts', 'public');

        GiftDesign::create([
            'item_name'            => $validated['item_name'],
            'category'             => $validated['category'],
            'material'             => $request->material,
            'size'                 => $request->size,
            'price'                => $validated['price'],
            'offer_price'          => $validated['offer_price'] ?? null,
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

        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Gift,Frame'],
            'price' => ['required', 'numeric', 'min:0'], 'offer_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            if ($imageName && Storage::disk('public')->exists($imageName)) {
                Storage::disk('public')->delete($imageName);
            }
            $imageName = $request->file('image')->store('gifts', 'public');
        }

        $gift->update([
            'item_name'            => $validated['item_name'],
            'category'             => $validated['category'],
            'material'             => $request->material,
            'size'                 => $request->size,
            'price'                => $validated['price'],
            'offer_price'          => $validated['offer_price'] ?? null,
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
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'laser_type'   => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'offer_price'  => 'nullable|numeric|min:0',
        ]);

        LaserWork::create([
            'product_name'     => $validated['product_name'],
            'laser_type'       => $validated['laser_type'],
            'material_type'    => $request->material_type,
            'product_category' => $request->product_category,
            'size'             => $request->size,
            'price'            => $validated['price'],
            'offer_price'      => $validated['offer_price'] ?? null,
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
        $validated = $request->validate([
            'product_name' => 'required|string|max:255', 'laser_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', 'offer_price' => 'nullable|numeric|min:0',
            'material_type' => 'nullable|string|max:255', 'product_category' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255', 'engraving_text' => 'nullable|string', 'description' => 'nullable|string',
        ]);
        $laserWork->update($validated);
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
        $validated = $request->validate([
            'event_name' => 'required|string|max:255', 'event_type' => 'required|string|max:255',
            'event_date' => 'required|date', 'price' => 'required|numeric|min:0', 'offer_price' => 'nullable|numeric|min:0',
            'decoration_type' => 'nullable|string|max:255', 'event_location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        foreach (['lighting_service', 'sound_service', 'dj_service', 'photography_service', 'cake_service'] as $field) {
            $validated[$field] = $request->boolean($field);
        }
        $event->update($validated);
        return redirect('/admin/services')->with('success', 'Event updated successfully!');
    }

    public function destroyEvent($id)
    {
        Event::findOrFail($id)->delete();
        return redirect('/admin/services')->with('success', 'Event deleted!');
    }
}
