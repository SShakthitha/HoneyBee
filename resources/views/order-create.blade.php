@extends('layouts.app')

@section('title', 'Place Order')

@section('content')

    <section style="padding: 60px 40px; max-width: 700px; margin: 0 auto;">

        <h1 style="font-size: 32px; margin-bottom: 10px;">Place Your <span style="color: #f5a623;">Order</span></h1>
        <p style="color: #777; margin-bottom: 30px;">You are ordering: <strong>{{ $service->service_name }}</strong></p>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">

            <form action="/orders" method="POST">
                @csrf

                <input type="hidden" name="service_id" value="{{ $service->service_id }}">

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Service</label>
                    <input type="text" value="{{ $service->service_name }}" disabled
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Amount (Rs.) *</label>
                    <input type="number" name="paid_amount" value="{{ $service->price }}" step="0.01" required
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Advanced Payment (Rs.)</label>
                    <input type="number" name="advanced_paid" step="0.01"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Payment Method *</label>
                    <select name="payment_method" required
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Delivery Date</label>
                    <input type="date" name="delivery_date"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Special Requirements</label>
                    <textarea name="attribute" rows="4"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;"
                        placeholder="Any special requirements or notes..."></textarea>
                </div>

                <button type="submit"
                    style="background: #f5a623; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 25px; font-weight: bold; font-size: 16px; cursor: pointer; width: 100%;">
                    🐝 Place Order
                </button>

            </form>
        </div>
    </section>

@endsection