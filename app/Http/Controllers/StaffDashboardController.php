<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\Staff;
use App\Services\OrderPaymentRecorder;
use App\Services\InventoryManager;
use App\Services\OrderStatusManager;
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

    public function updateOrderStatus(Request $request, $id, OrderStatusManager $statusManager, InventoryManager $inventory)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate(['status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])]]);
        $statusManager->update($order, $validated['status'], $inventory);

        return redirect()->route('staff.orders.show', $order->order_id)->with('success', 'Order status updated.');
    }

    public function updateOrderPayment(Request $request, int $id, OrderPaymentRecorder $paymentRecorder, InventoryManager $inventory)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'advanced_paid' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ]);

        $paymentRecorder->recordConfirmedAdvance($order, (string) $validated['advanced_paid'], $inventory);

        return redirect()->route('staff.orders.show', $order->order_id)->with('success', 'Confirmed advance payment updated.');
    }

    public function products(?string $type = null)
    {
        abort_if($type !== null && ! in_array($type, ['gift', 'laser', 'event'], true), 404);

        $definitions = [
            'gift' => ['title' => 'Gift & Design', 'model' => GiftDesign::class, 'name' => 'item_name', 'category' => 'category', 'key' => 'gift_design_id'],
            'laser' => ['title' => 'Laser Work', 'model' => LaserWork::class, 'name' => 'product_name', 'category' => 'product_category', 'key' => 'laser_id'],
            'event' => ['title' => 'Events', 'model' => Event::class, 'name' => 'event_name', 'category' => 'event_type', 'key' => 'event_id'],
        ];
        $sections = [];

        foreach ($definitions as $sectionType => $definition) {
            if ($type !== null && $sectionType !== $type) {
                continue;
            }

            $model = $definition['model'];
            $sections[$sectionType] = $definition + ['items' => $model::latest()->get()];
            unset($sections[$sectionType]['model']);
        }

        return view('staff.products', compact('sections', 'type'));
    }

    public function showProduct(string $type, $id)
    {
        abort_unless(in_array($type, ['gift', 'laser', 'event'], true), 404);

        return view('staff.product-show', compact('type') + ['model' => $this->product($type, $id)]);
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
        $previousImage = $model?->image;
        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')->store($type === 'gift' ? 'gifts' : $type, 'public');
            if (! $newImage) {
                return back()->withErrors(['image' => 'The image could not be stored.'])->withInput();
            }
            $attributes['image'] = $newImage;
        }

        $class = ['gift' => GiftDesign::class, 'laser' => LaserWork::class, 'event' => Event::class][$type];
        if ($model) {
            if (! $model->update($attributes)) {
                if ($newImage) {
                    Storage::disk('public')->delete($newImage);
                }

                return back()->withErrors(['product' => 'The product could not be saved.'])->withInput();
            }
        } else {
            $model = $class::create($attributes);
        }

        if ($newImage && $previousImage && $previousImage !== $newImage && Storage::disk('public')->exists($previousImage)) {
            Storage::disk('public')->delete($previousImage);
        }

        return redirect()->route('staff.products.category', $type)->with('success', ucfirst($type).' item saved successfully.');
    }

    public function deleteProduct($type, $id, InventoryManager $inventory)
    {
        $model = $this->product($type, $id);
        $inventoryType = $type === 'gift' ? (in_array(strtolower($model->category), ['frame', 'frames'], true) ? 'frame' : 'gift') : $type;
        $inventory->assertProductCanBeDeleted($inventoryType, (int) $model->getKey());
        if ($model->image && Storage::disk('public')->exists($model->image)) {
            Storage::disk('public')->delete($model->image);
        }
        $model->delete();

        return redirect()->route('staff.products.category', $type)->with('success', 'Product deleted.');
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
