<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_id',
        'service_id',
        'order_date',
        'payment_date',
        'paid_amount',
        'advanced_paid',
        'discount',
        'payment_method',
        'status',
        'delivery_date',
        'attribute',
        'feedback'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}