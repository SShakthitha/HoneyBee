<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaserWork extends Model
{
    protected $primaryKey = 'laser_id';

    protected $fillable = [
        'service_id',
        'product_name',
        'laser_type',
        'material_type',
        'product_category',
        'size',
        'price',
        'engraving_text',
        'description'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}