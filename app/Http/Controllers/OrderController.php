<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $customer = Customer::where('email', auth()->user()->email)
            ->firstOrFail();

        $orders = Order::with('items')
            ->where('customer_id', $customer->customer_id)
            ->latest('order_date')
            ->get();

        return view('orders', compact('orders'));
    }

    public function show($id)
    {
        $customer = Customer::where('email', auth()->user()->email)
            ->firstOrFail();

        $order = Order::with('items')
            ->where('customer_id', $customer->customer_id)
            ->where('order_id', $id)
            ->firstOrFail();

        return view('order-show', compact('order'));
    }

    public function downloadPdf(Request $request, $id)
    {
        $customer = Customer::where('email', $request->user()->email)->firstOrFail();

        $order = Order::with(['customer', 'items'])
            ->where('customer_id', $customer->customer_id)
            ->where('order_id', $id)
            ->firstOrFail();

        return $this->orderPdf($order);
    }

    private function orderPdf(Order $order)
    {
        $logoPath = public_path('images/logo.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;

        return Pdf::loadView('pdf.order-summary', compact('order', 'logo'))
            ->setPaper('a4')
            ->download('honeybee-order-'.$order->order_id.'.pdf');
    }

    public function create(Service $service)
    {
        return view('order-create', compact('service'));
    }

    /*
    |--------------------------------------------------------------------------
    | Existing service order
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $serviceId = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
        ])['service_id'];

        $service = Service::query()
            ->where('availability_status', 'available')
            ->findOrFail($serviceId);

        $totalCents = $this->amountInCents($service->price);
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'bank_transfer', 'card'])],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'attribute' => ['nullable', 'string', 'max:5000'],
        ]);

        $customer = Customer::where('email', $request->user()->email)
            ->firstOrFail();

        $order = DB::transaction(fn () => Order::create([
            'customer_id' => $customer->customer_id,
            'service_id' => $service->service_id,
            'order_date' => now(),
            'paid_amount' => $this->centsToAmount($totalCents),
            'advanced_paid' => '0.00',
            'discount' => '0.00',
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'delivery_date' => $validated['delivery_date'] ?? null,
            'attribute' => $validated['attribute'] ?? null,
        ]));

        return redirect('/orders')
            ->with('success', 'Order placed successfully!');
    }

    private function amountInCents(mixed $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function centsToAmount(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | Gift & Design Cart Order
    |--------------------------------------------------------------------------
    */

    public function storeCartOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.item_type' => 'required|in:gift,frame',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::where('email', $request->user()->email)
            ->firstOrFail();

        $items = collect($validated['items'])->map(function (array $item) {
            $product = GiftDesign::findOrFail($item['item_id']);

            abort_unless(strtolower($product->category) === $item['item_type'], 422);

            // Prices from the browser are never trusted. Use the product's
            // centralized offer-price rule so zero, equal, or higher offers
            // cannot reduce (or increase) the order total.
            $price = $product->currentSellingPrice();

            return [
                'item_id' => $product->gift_design_id,
                'item_type' => $item['item_type'],
                'item_name' => $product->item_name,
                'price' => $price,
                'quantity' => (int) $item['quantity'],
                'subtotal' => $price * (int) $item['quantity'],
            ];
        });

        $total = $items->sum('subtotal');

        $order = DB::transaction(function () use (
            $items,
            $customer,
            $total,
            $validated
        ) {
            $order = Order::create([
                'customer_id' => $customer->customer_id,
                'order_date' => now(),
                'paid_amount' => $total,
                'advanced_paid' => 0,
                'discount' => 0,
                'payment_method' => 'WhatsApp',
                'status' => 'pending',
                'attribute' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $customer->increment('total_spent', $total);

            return $order;
        });

        return redirect()
            ->route('checkout.confirmation', ['order' => $order->order_id]);
    }
}
