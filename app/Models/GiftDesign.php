<?php

namespace App\Models;

use App\Models\Concerns\HasOfferPricing;
use Illuminate\Database\Eloquent\Model;

class GiftDesign extends Model
{
    use HasOfferPricing;

    protected $primaryKey = 'gift_design_id';

    protected $fillable = [
        'item_name',
        'category',
        'material',
        'size',
        'price',
        'offer_price',
        'customization_option',
        'description',
        'image',
    ];
}
