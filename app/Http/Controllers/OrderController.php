<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;
use App\Models\Customer;

class OrderController extends Controller
{
    public function index()
    {
        $customer = Customer::where('email', auth()->user()->email)->firstOrFail();
        $orders = Order::with('service')->where('customer_id', $customer->customer_id)->latest('order_date')->get();
        return view('orders', compact('orders'));
    }

    public function create(Service $service)
    {
        return view('order-create', compact('service'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'payment_method' => 'required',
            'paid_amount' => 'required|numeric',
        ]);

        $customer = Customer::where('email', $request->user()->email)->firstOrFail();

        Order::create([
            'customer_id' => $customer->customer_id,
            'service_id' => $validated['service_id'],
            'order_date' => now(),
            'paid_amount' => $request->paid_amount,
            'advanced_paid' => $request->advanced_paid ?? 0,
            'discount' => $request->discount ?? 0,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'delivery_date' => $request->delivery_date,
            'attribute' => $request->attribute,
        ]);

        return redirect('/orders')->with('success', 'Order placed successfully! 🐝');
    }
}
