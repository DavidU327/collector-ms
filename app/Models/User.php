<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
    use Searchable;

    public $timestamps = false;

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'identification' => $this->identification,
            'phone' => $this->phone,
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function typeIdentification()
    {
        return $this->belongsTo(TypeIdentification::class);
    }

}
