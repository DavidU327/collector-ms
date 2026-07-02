<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{

    const ENABLED = 'Habilitado';
    const DISABLED = 'Deshabilitado';

    const PENDING_USER = 'Pendiente de Validar';

    const REJECT_USER = 'Rechazado';

    const DELETE_USER = 'Eliminado';

    public function users(){
        return $this->hasMany(User::class);
    }
}
