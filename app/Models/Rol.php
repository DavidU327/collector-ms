<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    const ADMIN = 'ADMIN';
    const USER = 'USER';
    const RECYCLER = 'RECYCLER';

    public function users(){
        return $this->hasMany(User::class);
    }
}
