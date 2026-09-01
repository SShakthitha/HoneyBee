<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px; }
        body { color: #2d2d2d; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { border-bottom: 3px solid #f5a623; padding-bottom: 16px; margin-bottom: 20px; }
        .logo { height: 48px; max-width: 140px; object-fit: contain; vertical-align: middle; margin-right: 12px; }
        .brand { color: #1a1a1a; display: inline-block; font-size: 22px; font-weight: bold; vertical-align: middle; }
        .brand small { color: #777; display: block; font-size: 10px; font-weight: normal; margin-top: 3px; }
        h2 { color: #1a1a1a; font-size: 15px; margin: 20px 0 8px; }
        .order-number { color: #a86700; float: right; font-size: 13px; font-weight: bold; margin-top: 10px; }
        .details { border-collapse: collapse; width: 100%; }
        .details td { border: 1px solid #e9e2d6; padding: 8px; width: 25%; }
        .details .label { background: #fffaf0; color: #666; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .items { border-collapse: collapse; margin-top: 8px; width: 100%; }
        .items th { background: #1a1a1a; color: #fff; font-size: 10px; padding: 9px 8px; text-align: left; }
        .items td { border-bottom: 1px solid #e6e6e6; padding: 9px 8px; }
        .right { text-align: right; }
        .total td { background: #fff4d7; font-weight: bold; }
        .notes { background: #fffaf0; border-left: 3px solid #f5a623; padding: 10px; }
        .footer { color: #777; font-size: 9px; margin-top: 28px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        @if($logo)<img class="logo" src="{{ $logo }}" alt="HoneyBee Shop logo">@endif
        <div class="brand">HoneyBee Shop<small>Order Summary</small></div>
        <div class="order-number">Order #{{ $order->order_id }}</div>
    </div>

    <h2>Customer &amp; Order Information</h2>
    <table class="details">
        <tr><td class="label">Customer ID</td><td>{{ $order->customer_id }}</td><td class="label">Order Date</td><td>{{ $order->order_date?->format('d M Y') ?? '-' }}</td></tr>
        <tr><td class="label">Customer Name</td><td>{{ $order->customer?->full_name ?? '-' }}</td><td class="label">Payment Method</td><td>{{ $order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : '-' }}</td></tr>
        <tr><td class="label">Email</td><td>{{ $order->customer?->email ?? '-' }}</td><td class="label">Order Status</td><td>{{ ucfirst($order->status) }}</td></tr>
        <tr><td class="label">Phone</td><td>{{ $order->customer?->phone ?? '-' }}</td><td class="label">Delivery Date</td><td>{{ $order->delivery_date?->format('d M Y') ?? 'Not set' }}</td></tr>
    </table>

    <h2>Ordered Items</h2>
    <table class="items">
        <thead><tr><th>Item</th><th class="right">Unit Price</th><th class="right">Quantity</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
            @forelse($order->items as $item)
                <tr><td>{{ $item->item_name }}</td><td class="right">Rs {{ number_format((float) $item->price, 2) }}</td><td class="right">{{ $item->quantity }}</td><td class="right">Rs {{ number_format((float) $item->subtotal, 2) }}</td></tr>
            @empty
                <tr><td colspan="4">No item details are available for this order.</td></tr>
            @endforelse
            <tr class="total"><td colspan="3" class="right">Order Total</td><td class="right">Rs {{ number_format((float) ($order->items->isNotEmpty() ? $order->items->sum('subtotal') : $order->paid_amount), 2) }}</td></tr>
        </tbody>
    </table>

    <h2>Special Requirements / Notes</h2>
    <div class="notes">{{ $order->attribute ?: 'None provided.' }}</div>
    <div class="footer">Thank you for choosing HoneyBee Shop.</div>
</body>
</html>
