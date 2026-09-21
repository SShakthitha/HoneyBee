<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSetting extends Model
{
    public const DEFAULT_ORDER_NUMBER = '94767158873';

    public const DEFAULT_MESSAGE_TEMPLATE = "Hello HoneyBee Shop,\n\nI would like to place an order.\n\nOrder ID: #{order_id}\n\nCustomer: {customer_name}\n\nItems:\n{items}\n\nTotal: Rs. {total}\n\nThank you.";

    protected $fillable = [
        'singleton_key',
        'order_number',
        'message_template',
    ];

    /** Return the application's sole settings record, creating defaults if needed. */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['singleton_key' => true],
            [
                'order_number' => self::DEFAULT_ORDER_NUMBER,
                'message_template' => self::DEFAULT_MESSAGE_TEMPLATE,
            ]
        );
    }
}
