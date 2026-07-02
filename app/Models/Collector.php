<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Collector extends Model
{
    use Searchable;

    public $timestamps = false;

    public function toSearchableArray()
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->user ? $this->user->name : '',
        ];
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentLocation()
    {
        return $this->hasOne(CollectorLocation::class)->latestOfMany();
    }

    public function locations(): HasMany
    {
        return $this->hasMany(CollectorLocation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
