<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WhatsAppSetting;

class WhatsAppOrderMessageBuilder
{
    public function messageFor(Order $order, ?WhatsAppSetting $settings = null): string
    {
        $settings ??= WhatsAppSetting::query()->firstOrFail();
        $order->loadMissing(['customer', 'items']);

        $items = $order->items->map(fn ($item) => sprintf(
            '- %s × %d — Rs. %s',
            $item->item_name,
            $item->quantity,
            $this->money($item->subtotal)
        ))->implode("\n");

        return strtr($settings->message_template, [
            '{order_id}' => (string) $order->order_id,
            '{customer_name}' => $order->customer?->full_name ?? 'Customer',
            '{items}' => $items,
            '{total}' => $this->money($order->paid_amount),
        ]);
    }

    public function urlFor(Order $order): ?string
    {
        $settings = WhatsAppSetting::query()->first();
        if (! $settings || blank($settings->order_number)) {
            return null;
        }

        $number = ltrim(preg_replace('/[\s\-()]/', '', $settings->order_number), '+');
        if (! preg_match('/^[1-9][0-9]{6,14}$/', $number)) {
            return null;
        }

        return 'https://wa.me/'.$number.'?'.http_build_query([
            'text' => $this->messageFor($order, $settings),
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function money(mixed $amount): string
    {
        return rtrim(rtrim(number_format((float) $amount, 2, '.', ''), '0'), '.');
    }
}
