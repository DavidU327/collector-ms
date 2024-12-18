<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public $timestamps = false;

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
