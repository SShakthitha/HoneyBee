<?php

namespace App\Models;

use App\Models\Concerns\HasOfferPricing;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasOfferPricing;

    protected $primaryKey = 'event_id';

    protected $fillable = [
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
        'offer_price',
        'description',
        'image',
        'booking_capacity',
        'reserved_slots',
        'booked_slots',
        'low_slots_threshold',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'lighting_service' => 'boolean',
            'sound_service' => 'boolean',
            'dj_service' => 'boolean',
            'photography_service' => 'boolean',
            'cake_service' => 'boolean',
        ];
    }

    public function getAvailableSlotsAttribute(): int
    {
        return max(0, (int) $this->booking_capacity - (int) $this->reserved_slots - (int) $this->booked_slots);
    }
}
