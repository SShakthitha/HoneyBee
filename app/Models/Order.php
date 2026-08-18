<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Service;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_id',
        'order_date',
        'payment_date',
        'paid_amount',
        'advanced_paid',
        'discount',
        'payment_method',
        'status',
        'delivery_date',
        'attribute',
        'feedback',
    ];

    protected $casts = [
        'order_date' => 'date',
        'payment_date' => 'date',
        'delivery_date' => 'date',
        'paid_amount' => 'decimal:2',
        'advanced_paid' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}