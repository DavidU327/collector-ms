<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectorLocation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'collector_id',
        'latitude',
        'longitude',
        'updated_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class);
    }
}
