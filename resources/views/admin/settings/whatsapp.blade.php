@extends('layouts.admin')

@section('title', 'WhatsApp Order Settings')

@section('content')
<div class="topbar">
    <div><h1>WhatsApp <span>Order Settings</span></h1><p class="text-muted mb-0">Set the number and message used after a customer has placed an order.</p></div>
</div>

<div class="admin-card form-box">
    <div class="admin-card-body">
        <form method="POST" action="{{ route('admin.settings.whatsapp.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="form-label" for="order_number">WhatsApp Order Number</label>
                <input class="form-control @error('order_number') is-invalid @enderror" id="order_number" name="order_number" type="tel" inputmode="tel" value="{{ old('order_number', '+' . $settings->order_number) }}" placeholder="+94767158873" required>
                <div class="form-text">Use an international number. Spaces, hyphens and parentheses are removed when saved.</div>
                @error('order_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label" for="message_template">WhatsApp Order Message</label>
                <textarea class="form-control @error('message_template') is-invalid @enderror" id="message_template" name="message_template" rows="12" required>{{ old('message_template', $settings->message_template) }}</textarea>
                <div class="form-text">Supported placeholders: <code>{order_id}</code>, <code>{customer_name}</code>, <code>{items}</code>, <code>{total}</code>.</div>
                @error('message_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-honey px-4" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i> Save Settings</button>
        </form>
    </div>
</div>
@endsection
