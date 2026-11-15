<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    public const DESTINATION_ROUTES = [
        'home',
        'gift.design',
        'events',
        'laser.work',
    ];

    protected $fillable = [
        'title',
        'short_description',
        'image',
        'button_text',
        'destination',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeActive($query)
    {
        $today = now()->toDateString();

        return $query
            ->where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $today);
            });
    }

    public static function destinationRoutes(): array
    {
        return self::DESTINATION_ROUTES;
    }

    public function destinationRoute(): string
    {
        return in_array($this->destination, self::DESTINATION_ROUTES, true)
            ? $this->destination
            : 'gift.design';
    }
}
