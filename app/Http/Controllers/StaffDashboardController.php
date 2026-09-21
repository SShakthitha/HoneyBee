<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StaffDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $recentOrders = Order::with(['customer', 'items'])->latest('order_date')->take(5)->get();
        $products = collect()
            ->merge(GiftDesign::latest()->take(3)->get()->map(fn ($item) => (object) ['name' => $item->item_name, 'type' => $item->category, 'created_at' => $item->created_at]))
            ->merge(LaserWork::latest()->take(3)->get()->map(fn ($item) => (object) ['name' => $item->product_name, 'type' => 'Laser Work', 'created_at' => $item->created_at]))
            ->merge(Event::latest()->take(3)->get()->map(fn ($item) => (object) ['name' => $item->event_name, 'type' => 'Event', 'created_at' => $item->created_at]))
            ->sortByDesc('created_at')->take(5);

        return view('staff.dashboard', [
            'staff' => $this->currentStaff($request),
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'processingOrders' => Order::where('status', 'processing')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
            'totalProducts' => GiftDesign::count() + LaserWork::count() + Event::count(),
            'galleryItems' => GalleryImage::count(),
            'recentOrders' => $recentOrders,
            'recentProducts' => $products,
            'recentGallery' => GalleryImage::latest()->take(5)->get(),
        ]);
    }

    public function orders()
    {
        return view('staff.orders', ['orders' => Order::with(['customer', 'items'])->latest('order_date')->paginate(15)]);
    }

    public function showOrder($id)
    {
        return view('staff.order-show', ['order' => Order::with(['customer', 'items'])->where('order_id', $id)->firstOrFail()]);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->validate(['status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])]]));

        return redirect()->route('staff.orders.show', $order->order_id)->with('success', 'Order status updated.');
    }

    public function products()
    {
        return view('staff.products', ['gifts' => GiftDesign::latest()->get(), 'lasers' => LaserWork::latest()->get(), 'events' => Event::latest()->get()]);
    }

    public function productForm($type, $id = null)
    {
        abort_unless(in_array($type, ['gift', 'laser', 'event'], true), 404);
        $model = $id ? $this->product($type, $id) : null;

        return view('staff.product-form', compact('type', 'model'));
    }

    public function saveProduct(Request $request, $type, $id = null)
    {
        abort_unless(in_array($type, ['gift', 'laser', 'event'], true), 404);
        $model = $id ? $this->product($type, $id) : null;
        $data = $this->validateProduct($request, $type);
        // Uploaded files are handled separately so an edit without a new image
        // keeps the existing public-disk image path intact.
        unset($data['image']);
        $attributes = $this->productAttributes($request, $type, $data);

        if ($request->hasFile('image')) {
            if ($model?->image && Storage::disk('public')->exists($model->image)) {
                Storage::disk('public')->delete($model->image);
            }
            $attributes['image'] = $request->file('image')->store($type === 'gift' ? 'gifts' : $type, 'public');
        }

        $class = ['gift' => GiftDesign::class, 'laser' => LaserWork::class, 'event' => Event::class][$type];
        $model ? $model->update($attributes) : $class::create($attributes);

        return redirect()->route('staff.products.index')->with('success', ucfirst($type).' item saved successfully.');
    }

    public function deleteProduct($type, $id)
    {
        $model = $this->product($type, $id);
        if ($model->image && Storage::disk('public')->exists($model->image)) {
            Storage::disk('public')->delete($model->image);
        }
        $model->delete();

        return redirect()->route('staff.products.index')->with('success', 'Product deleted.');
    }

    public function gallery()
    {
        return view('staff.gallery', ['images' => GalleryImage::latest()->get()]);
    }

    public function galleryForm($id = null)
    {
        return view('staff.gallery-form', ['image' => $id ? GalleryImage::findOrFail($id) : null]);
    }

    public function saveGallery(Request $request, $id = null)
    {
        $image = $id ? GalleryImage::findOrFail($id) : new GalleryImage;
        $data = $request->validate(['title' => 'nullable|string|max:255', 'category' => ['required', Rule::in(['Gift & Design', 'Frame Designs', 'Laser Work', 'Events'])], 'image' => [$id ? 'nullable' : 'required', 'image', 'max:2048']]);
        if ($request->hasFile('image')) {
            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            $data['image'] = $request->file('image')->store('gallery', 'public');
        }
        $image->fill($data)->save();

        return redirect()->route('staff.gallery.index')->with('success', 'Gallery item saved successfully.');
    }

    public function deleteGallery($id)
    {
        $image = GalleryImage::findOrFail($id);
        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }
        $image->delete();

        return redirect()->route('staff.gallery.index')->with('success', 'Gallery item deleted.');
    }

    public function profile(Request $request)
    {
        return view('staff.profile', ['staff' => $this->currentStaff($request)]);
    }

    public function updateProfile(Request $request)
    {
        $staff = $this->currentStaff($request);
        $validated = $request->validate(['full_name' => 'required|string|max:255', 'phone' => 'required|string|max:30']);
        $staff->update($validated);

        return redirect()->route('staff.profile')->with('success', 'Your staff profile was updated.');
    }

    private function currentStaff(Request $request): Staff
    {
        return Staff::where('email', $request->user()->email)->whereIn('role', ['staff', 'manager'])->firstOrFail();
    }

    private function product(string $type, $id)
    {
        return ['gift' => GiftDesign::class, 'laser' => LaserWork::class, 'event' => Event::class][$type]::findOrFail($id);
    }

    private function validateProduct(Request $request, string $type): array
    {
        $rules = ['price' => 'required|numeric|min:0', 'offer_price' => 'nullable|numeric|min:0', 'image' => 'nullable|image|max:10240'];

        return $request->validate($rules + match ($type) {
            'gift' => ['item_name' => 'required|string|max:255', 'category' => ['required', Rule::in(['Gift', 'Frame'])]],
            'laser' => ['product_name' => 'required|string|max:255', 'laser_type' => 'required|string|max:255'],
            'event' => ['event_name' => 'required|string|max:255', 'event_type' => 'required|string|max:255', 'event_date' => 'required|date'],
        });
    }

    private function productAttributes(Request $request, string $type, array $data): array
    {
        return match ($type) {
            'gift' => $data + $request->only(['material', 'size', 'customization_option', 'description']),
            'laser' => $data + $request->only(['material_type', 'product_category', 'size', 'engraving_text', 'description']),
            'event' => $data + $request->only(['decoration_type', 'event_location', 'description']) + collect(['lighting_service', 'sound_service', 'dj_service', 'photography_service', 'cake_service'])->mapWithKeys(fn ($field) => [$field => $request->boolean($field)])->all(),
        };
    }
}
