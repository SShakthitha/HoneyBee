<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'business_id',
        'service_name',
        'service_type',
        'description',
        'availability_status',
        'price',
        'category',
        'served_date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function giftDesign()
    {
        return $this->hasOne(GiftDesign::class, 'service_id');
    }

    public function laserWork()
    {
        return $this->hasOne(LaserWork::class, 'service_id');
    }

    public function event()
    {
        return $this->hasOne(Event::class, 'service_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'service_id');
    }

    public function handles()
    {
        return $this->hasMany(Handles::class, 'service_id');
    }
}