<?php

namespace App\Models;

use App\Models\Concerns\HasOfferPricing;
use Illuminate\Database\Eloquent\Model;

class LaserWork extends Model
{
    use HasOfferPricing;

    protected $primaryKey = 'laser_id';

    protected $fillable = [
        'product_name',
        'laser_type',
        'material_type',
        'product_category',
        'size',
        'price',
        'offer_price',
        'engraving_text',
        'description', 'image',
        'production_type',
        'stock_quantity',
        'reserved_quantity',
        'low_stock_threshold',
    ];

    public function getAvailableStockAttribute(): int
    {
        return max(0, (int) $this->stock_quantity - (int) $this->reserved_quantity);
    }
}
