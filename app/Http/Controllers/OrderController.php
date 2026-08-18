<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\Customer;

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
        $validated = $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'payment_method' => 'required',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $customer = Customer::where('email', $request->user()->email)
            ->firstOrFail();

        $order = Order::create([
            'customer_id' => $customer->customer_id,
            'order_date' => now(),
            'paid_amount' => $validated['paid_amount'],
            'advanced_paid' => $request->advanced_paid ?? 0,
            'discount' => $request->discount ?? 0,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'delivery_date' => $request->delivery_date,
            'attribute' => $request->attribute,
        ]);

        return redirect('/orders')
            ->with('success', 'Order placed successfully!');
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
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_id' => 'nullable|integer',
            'items.*.item_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::where('email', $request->user()->email)
            ->firstOrFail();

        $total = 0;

        foreach ($validated['items'] as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        $order = DB::transaction(function () use (
            $validated,
            $customer,
            $total
        ) {
            $order = Order::create([
                'customer_id' => $customer->customer_id,
                'order_date' => now(),
                'paid_amount' => 0,
                'advanced_paid' => 0,
                'discount' => 0,
                'payment_method' => 'WhatsApp',
                'status' => 'pending',
                'attribute' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $price = (float) $item['price'];

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'item_type' => $item['item_type'] ?? 'product',
                    'item_id' => $item['item_id'] ?? null,
                    'item_name' => $item['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('orders.index')
            ->with('success', 'Your order has been placed successfully!');
    }

  
}