<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTypeWaste extends Model
{
    protected $fillable = [
        'order_id',
        'type_waste_id',
        'weight',
        'points',
    ];

    public $timestamps = false;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function typeWaste(): BelongsTo
    {
        return $this->belongsTo(TypeWaste::class);
    }
}
