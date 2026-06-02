<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Handles extends Model
{
    protected $primaryKey = 'handles_id';

    protected $fillable = [
        'staff_id',
        'service_id',
        'assigned_date',
        'start_date',
        'end_date',
        'workload',
        'status',
        'feedback',
        'attribute'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}