<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustment extends Model
{
    protected $fillable = [
        'item_type', 'item_id', 'adjustment_type', 'quantity_before',
        'quantity_after', 'quantity_delta', 'note', 'adjusted_by',
    ];

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}
