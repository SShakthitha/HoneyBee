<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $customer = Customer::where('email', $user->email)->first();

        return view('customer.dashboard', compact('customer'));
    }
}