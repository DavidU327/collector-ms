<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{

    const ENABLED = 'Habilitado';
    const DISABLED = 'Deshabilitado';

    public function users(){
        return $this->hasMany(User::class);
    }
}
