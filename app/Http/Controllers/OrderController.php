<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Service;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('customer_id', auth()->id())->get();
        return view('orders', compact('orders'));
    }

    public function create($service_id)
    {
        $service = Service::findOrFail($service_id);
        return view('order-create', compact('service'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'payment_method' => 'required',
            'paid_amount' => 'required|numeric',
        ]);

        Order::create([
            'customer_id' => auth()->id(),
            'service_id' => $request->service_id,
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