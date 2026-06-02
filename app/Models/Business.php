<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $primaryKey = 'business_id';

    protected $fillable = [
        'business_name',
        'business_type',
        'description',
        'contact_email',
        'phone'
    ];

    public function staff()
    {
        return $this->hasMany(Staff::class, 'business_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'business_id');
    }
}