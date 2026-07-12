<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaserWork extends Model
{
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
        'description'
    ];

}