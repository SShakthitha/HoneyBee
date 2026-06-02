<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'service_id',
        'event_name',
        'event_type',
        'decoration_type',
        'lighting_service',
        'sound_service',
        'dj_service',
        'photography_service',
        'cake_service',
        'event_date',
        'event_location',
        'price',
        'description'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}