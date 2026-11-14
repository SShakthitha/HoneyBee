<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\GiftDesign;
use App\Models\Handles;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Rules\ValidGiftImage;
use App\Services\OrderPaymentRecorder;
use App\Services\OrderStatusManager;
use App\Services\InventoryManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

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
        $businesses = Business::query()
            ->select('business_id', 'business_name', 'contact_email', 'phone', 'description')
            ->withCount(['staff', 'services']);

        return DataTables::of($businesses)
            ->addColumn('action', function ($b) {
                $canDelete = $b->staff_count === 0 && $b->services_count === 0;
                $deleteTitle = $canDelete
                    ? 'Delete this unreferenced business'
                    : 'Deletion is blocked while this business has linked staff or services';

                return '
                    <div class="d-flex justify-content-end gap-1">
                        <button type="button"
                            class="btn btn-honey btn-sm editBtn"
                            data-id="'.$b->business_id.'">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button"
                            class="btn btn-outline-danger btn-sm deleteBtn" '.($canDelete ? '' : 'disabled aria-disabled="true"').'
                            title="'.$deleteTitle.'"
                            data-id="'.$b->business_id.'">
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
            'phone' => 'required',
        ]);

        Business::create([
            'business_name' => $request->business_name,
            'contact_email' => $request->contact_email,
            'phone' => $request->phone,
            'description' => $request->description,
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
        $blockers = DB::transaction(function () use ($id): array {
            $business = Business::where('business_id', $id)->lockForUpdate()->firstOrFail();
            $businessId = $business->business_id;
            $blockers = [];

            if ($business->staff()->exists()) {
                $blockers[] = 'staff';
            }

            if ($business->services()->exists()) {
                $blockers[] = 'services';
            }

            $hasAssignments = Handles::query()
                ->whereHas('staff', fn ($query) => $query->where('business_id', $businessId))
                ->orWhereHas('service', fn ($query) => $query->where('business_id', $businessId))
                ->exists();

            if ($hasAssignments) {
                $blockers[] = 'staff assignments';
            }

            $hasOrders = Order::query()
                ->whereHas('service', fn ($query) => $query->where('business_id', $businessId))
                ->exists();

            if ($hasOrders) {
                $blockers[] = 'historical orders';
            }

            if ($blockers !== []) {
                return $blockers;
            }

            $business->delete();

            return [];
        });

        if ($blockers !== []) {
            return back()->withErrors([
                'business' => 'This business cannot be deleted because it has associated '.implode(', ', $blockers).'. The related records and order history have been preserved.',
            ]);
        }

        return redirect()->back()->with('success', 'Business deleted!');
    }

    // ─── Services ─────────────────────────────────────────────
    public function services()
    {
        return redirect()->route('admin.services.gift');
    }

    public function serviceSection(string $section)
    {
        $config = match ($section) {
            'gift-design' => ['title' => 'Gift & Design', 'type' => 'gift', 'items' => GiftDesign::latest()->get()],
            'events' => ['title' => 'Events', 'type' => 'event', 'items' => Event::latest()->get()],
            'laser-work' => ['title' => 'Laser Work', 'type' => 'laser', 'items' => LaserWork::latest()->get()],
            default => abort(404),
        };

        return view('admin.services-section', $config);
    }

    // ─── Orders ───────────────────────────────────────────────
    public function orders()
    {
        $orders = Order::with(['customer', 'items'])
            ->latest('order_date')
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function updateOrderPayment(Request $request, int $id, OrderPaymentRecorder $paymentRecorder, InventoryManager $inventory)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'advanced_paid' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ]);

        $paymentRecorder->recordConfirmedAdvance($order, (string) $validated['advanced_paid'], $inventory);

        return redirect()->route('admin.orders.show', $order->order_id)->with('success', 'Confirmed advance payment updated.');
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
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        return Pdf::loadView('pdf.order-summary', compact('order', 'logo'))
            ->setPaper('a4')
            ->download('honeybee-order-'.$order->order_id.'.pdf');
    }

    public function updateOrderStatus(Request $request, $id, OrderStatusManager $statusManager, InventoryManager $inventory)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
        ]);

        $statusManager->update($order, $validated['status'], $inventory);

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
                if (! $order->customer) {
                    return '<span class="text-muted">Unknown Customer</span>';
                }

                $html = '<strong>'.e($order->customer->full_name).'</strong>';

                if ($order->customer->email) {
                    $html .= '<br><small class="text-muted">'
                        .e($order->customer->email)
                        .'</small>';
                }

                if ($order->customer->phone) {
                    $html .= '<br><small class="text-muted">'
                        .e($order->customer->phone)
                        .'</small>';
                }

                return $html;
            })

            ->addColumn('date', function ($order) {
                return $order->order_date
                    ? $order->order_date->format('Y-m-d')
                    : '-';
            })

            ->addColumn('items', function ($order) {
                if (! $order->items || $order->items->isEmpty()) {
                    return '<span class="text-muted">No items</span>';
                }

                $html = '';

                foreach ($order->items as $item) {
                    $html .= '<div class="mb-2">';
                    $html .= '<strong>'.e($item->item_name).'</strong>';
                    $html .= '<br>';
                    $html .= '<small class="text-muted">';
                    $html .= 'Qty: '.(int) $item->quantity;
                    $html .= ' × Rs '.number_format((float) $item->price, 2);
                    $html .= '</small>';
                    $html .= '</div>';
                }

                return $html;
            })

            ->addColumn('total', function ($order) {
                $total = $order->items->sum(function ($item) {
                    return (float) $item->subtotal;
                });

                return '<strong>Rs '.number_format($total, 2).'</strong>';
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
                        background:'.$background.';
                        color:'.$color.';
                        font-size:13px;
                        font-weight:bold;
                    ">
                        '.ucfirst(e($order->status)).'
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
                            href="'.$viewUrl.'"
                            class="btn btn-sm btn-outline-primary"
                            title="View Order"
                        >
                            <i class="fa-solid fa-eye"></i>
                            View
                        </a>

                        <form
                            method="POST"
                            action="'.route('admin.orders.status', $order->order_id).'"
                            style="margin:0;"
                        >
                            '.csrf_field().'
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
                        <option value="'.$value.'" '.$selected.'>
                            '.$label.'
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
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        Customer::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_spent' => 0,
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
            'email' => 'required|email|unique:customers,email,'.$id.',customer_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
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
                ->orWhere('full_name', 'like', '%'.$request->search.'%')
                ->orWhere('role', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->orWhere('phone', 'like', '%'.$request->search.'%');
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
            'role' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'hire_date' => 'required|date',
        ]);

        Staff::create([
            'business_id' => 1,
            'full_name' => $request->full_name,
            'role' => $request->role,
            'email' => $request->email,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
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
    public function createGift(Request $request)
    {
        $services = Service::where('service_type', 'gift')->get();
        $galleryImage = null;
        $defaultCategory = 'Gift';

        if ($request->filled('gallery_image_id')) {
            $galleryImage = GalleryImage::query()
                ->whereIn('category', ['Gift & Design', 'Frame Designs'])
                ->findOrFail($request->input('gallery_image_id'));
            $defaultCategory = $galleryImage->category === 'Frame Designs' ? 'Frame' : 'Gift';
        }

        return view('admin.gift.create', compact('services', 'galleryImage', 'defaultCategory'));
    }

    public function storeGift(Request $request)
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Gift,Frame'],
            'price' => ['required', 'numeric', 'min:0'], 'offer_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'file', 'max:10240', new ValidGiftImage],
            'gallery_image_id' => [
                'nullable',
                'integer',
                Rule::exists('gallery_images', 'id')
                    ->where(fn ($query) => $query->whereIn('category', ['Gift & Design', 'Frame Designs'])),
            ],
        ]);

        $galleryImage = ! empty($validated['gallery_image_id'])
            ? GalleryImage::findOrFail($validated['gallery_image_id'])
            : null;
        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->store('gifts', 'public');

            if (! $imageName) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }
        } elseif ($galleryImage) {
            $publicDisk = Storage::disk('public');

            if (! $publicDisk->exists($galleryImage->image)) {
                return back()->withErrors(['image' => 'The selected gallery image is no longer available.'])->withInput();
            }

            $extension = pathinfo($galleryImage->image, PATHINFO_EXTENSION);
            $imageName = 'gifts/'.Str::uuid().($extension ? '.'.$extension : '');

            if (! $publicDisk->copy($galleryImage->image, $imageName)) {
                return back()->withErrors(['image' => 'The selected gallery image could not be copied.'])->withInput();
            }
        }


        GiftDesign::create([
            'item_name' => $validated['item_name'],
            'category' => $galleryImage
                ? ($galleryImage->category === 'Frame Designs' ? 'Frame' : 'Gift')
                : $validated['category'],
            'material' => $request->material,
            'size' => $request->size,
            'price' => $validated['price'],
            'offer_price' => $validated['offer_price'] ?? null,
            'customization_option' => $request->customization_option,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return redirect()->route('admin.services.gift')->with('success', 'Gift item added successfully!');
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
        $oldImageName = $gift->image;
        $imageName = $oldImageName;

        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Gift,Frame'],
            'price' => ['required', 'numeric', 'min:0'], 'offer_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'file', 'max:10240', new ValidGiftImage],
        ]);

        if ($request->hasFile('image')) {
            $newImageName = $request->file('image')->store('gifts', 'public');

            if (! $newImageName) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }

            $imageName = $newImageName;
        }

        $gift->update([
            'item_name' => $validated['item_name'],
            'category' => $validated['category'],
            'material' => $request->material,
            'size' => $request->size,
            'price' => $validated['price'],
            'offer_price' => $validated['offer_price'] ?? null,
            'customization_option' => $request->customization_option,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        if ($request->hasFile('image') && $oldImageName && Storage::disk('public')->exists($oldImageName)) {
            Storage::disk('public')->delete($oldImageName);
        }

        return redirect()->route('admin.services.gift')->with('success', 'Gift item updated successfully!');
    }

    public function destroyGift($id, InventoryManager $inventory)
    {
        $gift = GiftDesign::findOrFail($id);
        $inventory->assertProductCanBeDeleted(in_array(strtolower($gift->category), ['frame', 'frames'], true) ? 'frame' : 'gift', (int) $gift->gift_design_id);

        if ($gift->image && Storage::disk('public')->exists($gift->image)) {
            Storage::disk('public')->delete($gift->image);
        }

        $gift->delete();

        return redirect()->route('admin.services.gift')->with('success', 'Gift item deleted!');
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
            'laser_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ]);

        LaserWork::create([
            'product_name' => $validated['product_name'],
            'laser_type' => $validated['laser_type'],
            'material_type' => $request->material_type,
            'product_category' => $request->product_category,
            'size' => $request->size,
            'price' => $validated['price'],
            'offer_price' => $validated['offer_price'] ?? null,
            'engraving_text' => $request->engraving_text,
            'description' => $request->description,
            'image' => $request->file('image')?->store('laser', 'public'),
        ]);

        return redirect()->route('admin.services.laser')->with('success', 'Laser work item added successfully!');
    }

    public function editLaser($id)
    {
        $laserWork = LaserWork::findOrFail($id);

        return view('admin.laser.edit', compact('laserWork'));
    }

    public function updateLaser(Request $request, $id)
    {
        $laserWork = LaserWork::findOrFail($id);
        $oldImage = $laserWork->image;
        $newImage = null;
        $validated = $request->validate([
            'product_name' => 'required|string|max:255', 'laser_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', 'offer_price' => 'nullable|numeric|min:0',
            'material_type' => 'nullable|string|max:255', 'product_category' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255', 'engraving_text' => 'nullable|string', 'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);
        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('laser', 'public');
            if (! $newImage) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }
            $validated['image'] = $newImage;
        }
        if (! $laserWork->update($validated)) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            return back()->withErrors(['product' => 'The product could not be saved.'])->withInput();
        }
        if ($newImage && $oldImage && $oldImage !== $newImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.services.laser')->with('success', 'Laser work updated successfully!');
    }

    public function destroyLaser($id, InventoryManager $inventory)
    {
        $laserWork = LaserWork::findOrFail($id);
        $inventory->assertProductCanBeDeleted('laser', (int) $laserWork->laser_id);
        if ($laserWork->image && Storage::disk('public')->exists($laserWork->image)) {
            Storage::disk('public')->delete($laserWork->image);
        }
        $laserWork->delete();

        return redirect()->route('admin.services.laser')->with('success', 'Laser work deleted!');
    }

    // ─── Events ───────────────────────────────────────────────
    public function createEvent()
    {
        return view('admin.event.create');
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:10240',
        ]);

        Event::create([
            'event_name' => $validated['event_name'],
            'event_type' => $validated['event_type'],
            'decoration_type' => $request->decoration_type,
            'lighting_service' => $request->has('lighting_service'),
            'sound_service' => $request->has('sound_service'),
            'dj_service' => $request->has('dj_service'),
            'photography_service' => $request->has('photography_service'),
            'cake_service' => $request->has('cake_service'),
            'event_date' => $validated['event_date'],
            'event_location' => $request->event_location,
            'price' => $validated['price'],
            'offer_price' => $validated['offer_price'] ?? null,
            'description' => $request->description,
            'image' => $request->file('image')?->store('events', 'public'),
        ]);

        return redirect()->route('admin.services.events')->with('success', 'Event added successfully!');
    }

    public function editEvent($id)
    {
        $event = Event::findOrFail($id);

        return view('admin.event.edit', compact('event'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $oldImage = $event->image;
        $newImage = null;
        $validated = $request->validate([
            'event_name' => 'required|string|max:255', 'event_type' => 'required|string|max:255',
            'event_date' => 'required|date', 'price' => 'required|numeric|min:0', 'offer_price' => 'nullable|numeric|min:0',
            'decoration_type' => 'nullable|string|max:255', 'event_location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);
        foreach (['lighting_service', 'sound_service', 'dj_service', 'photography_service', 'cake_service'] as $field) {
            $validated[$field] = $request->boolean($field);
        }
        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store('events', 'public');
            if (! $newImage) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }
            $validated['image'] = $newImage;
        }
        if (! $event->update($validated)) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            return back()->withErrors(['product' => 'The event could not be saved.'])->withInput();
        }
        if ($newImage && $oldImage && $oldImage !== $newImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.services.events')->with('success', 'Event updated successfully!');
    }

    public function destroyEvent($id, InventoryManager $inventory)
    {
        $event = Event::findOrFail($id);
        $inventory->assertProductCanBeDeleted('event', (int) $event->event_id);
        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();

        return redirect()->route('admin.services.events')->with('success', 'Event deleted!');
    }
}
