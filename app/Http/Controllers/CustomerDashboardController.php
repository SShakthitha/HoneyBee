<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Breeze authenticates the users table, while orders belong to the
        // separate customers table. They are linked by the registered email.
        $customer = Customer::where('email', $user->email)->first();

        $orders = $customer
            ? Order::where('customer_id', $customer->customer_id)
                ->latest('order_date')
                ->get()
            : collect();

        // Dashboard statistics
        $totalOrders = $orders->count();

        $pendingOrders = $orders
            ->where('status', 'pending')
            ->count();

        $completedOrders = $orders
            ->where('status', 'completed')
            ->count();

        $totalSpent = $orders->sum('paid_amount');

        return view('dashboard', compact(
            'customer',
            'user',
            'orders',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalSpent'
        ));
    }
}
