<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;


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

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
