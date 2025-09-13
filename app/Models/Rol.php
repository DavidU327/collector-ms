<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    public const ADMIN = 'Administrador';
    public const USER = 'Usuario';
    public const RECYCLER = 'Recolector';

    public function users(){
        return $this->hasMany(User::class);
    }
}
