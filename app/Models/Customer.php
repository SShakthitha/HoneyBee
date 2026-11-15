<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';
    
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
        'total_spent',
        'registered_date'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}