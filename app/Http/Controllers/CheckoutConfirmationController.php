<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Services\WhatsAppOrderMessageBuilder;
use Illuminate\Http\Request;

class CheckoutConfirmationController extends Controller
{
    public function show(Request $request, Order $order, WhatsAppOrderMessageBuilder $messageBuilder)
    {
        $customer = Customer::where('email', $request->user()->email)->firstOrFail();

        abort_unless($order->customer_id === $customer->customer_id, 404);

        $order->loadMissing(['customer', 'items']);

        // WhatsApp is an optional follow-up only. A malformed/missing setting
        // must never make a successfully committed order inaccessible.
        try {
            $whatsAppUrl = $messageBuilder->urlFor($order);
        } catch (\Throwable $exception) {
            report($exception);
            $whatsAppUrl = null;
        }

        return view('checkout-confirmation', compact('order', 'whatsAppUrl'));
    }
}
