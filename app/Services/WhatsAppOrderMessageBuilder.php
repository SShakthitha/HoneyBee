<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\WhatsAppSetting;

class WhatsAppOrderMessageBuilder
{
    public function messageFor(Order $order, ?WhatsAppSetting $settings = null): string
    {
        $settings ??= WhatsAppSetting::query()->first();
        $order->loadMissing(['customer', 'items']);

        $items = $order->items->map(fn ($item) => sprintf(
            '- %s × %d — Rs. %s',
            $item->item_name,
            $item->quantity,
            $this->money($item->subtotal)
        ))->implode("\n");

        return strtr($settings?->message_template ?? WhatsAppSetting::DEFAULT_MESSAGE_TEMPLATE, [
            '{order_id}' => (string) $order->order_id,
            '{customer_name}' => $order->customer?->full_name ?? 'Customer',
            '{items}' => $items,
            '{total}' => $this->money($order->paid_amount),
        ]);
    }

    public function urlFor(Order $order): ?string
    {
        $settings = WhatsAppSetting::query()->first();
        $number = (string) config('honeybee.contact.whatsapp_number');
        if (! preg_match('/^[1-9][0-9]{6,14}$/', $number)) {
            return null;
        }

        return 'https://wa.me/'.$number.'?'.http_build_query([
            'text' => $this->messageFor($order, $settings),
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Build a WhatsApp request without representing it as a saved or confirmed order.
     * Inventory is deliberately not claimed as checked or reserved here.
     *
     * @param  iterable<array{item_type:string,item_name:string,price:string,quantity:int,subtotal:string}>  $items
     */
    public function urlForCheckoutRequest(Customer $customer, iterable $items, string $notes, string $total): ?string
    {
        $number = (string) config('honeybee.contact.whatsapp_number');
        if (! preg_match('/^[1-9][0-9]{6,14}$/', $number)) {
            return null;
        }

        $lines = collect($items)->map(fn (array $item) => sprintf(
            '- %s: %s | Quantity: %d | Unit price: Rs. %s | Subtotal: Rs. %s',
            $item['item_type'] === 'frame' ? 'Frame' : 'Gift',
            $item['item_name'],
            $item['quantity'],
            $this->money($item['price']),
            $this->money($item['subtotal']),
        ))->implode("\n");

        $contact = collect(array_filter([
            $customer->phone ? 'Phone: '.$customer->phone : null,
            $customer->email ? 'Email: '.$customer->email : null,
        ]))->implode(' | ');

        $message = implode("\n", array_filter([
            'HONEYBEE ORDER REQUEST — not yet saved or confirmed',
            'Customer: '.($customer->full_name ?: 'Customer'),
            'Contact: '.($contact ?: 'Not provided'),
            'Items:',
            $lines,
            'Total: Rs. '.$this->money($total),
            $notes !== '' ? 'Customer notes: '.$notes : null,
            'Order reference: Not assigned. No order has been saved and no payment has been recorded.',
            'Availability: Stock has not been checked or reserved. Please confirm availability with HoneyBee.',
            'Payment: Bank transfer only after HoneyBee confirms the request. Do not treat this message as payment confirmation.',
        ]));

        return 'https://wa.me/'.$number.'?'.http_build_query([
            'text' => $message,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function money(mixed $amount): string
    {
        return rtrim(rtrim(number_format((float) $amount, 2, '.', ''), '0'), '.');
    }
}
