<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'business_id',
        'full_name',
        'role',
        'email',
        'phone',
        'hire_date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function handles()
    {
        return $this->hasMany(Handles::class, 'staff_id');
    }
}