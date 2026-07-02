<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\State;
use App\Models\Collector;

class ChangeStateCollectorIndexController extends Controller
{
    public function changeState(State $state, Collector $collector)
    {
        $collector->state_id = $state->id;
        $collector->save();
        $user = User::find($collector->user_id);
        $user->state_id = $state->id;
        $user->save();
        $data = [
            'message' => 'Cambio de estado correctamente',
            'data' => [
                'id' => $collector->id,
                'state' => [
                    'id' => $state->id,
                    'name' => $state->name,
                    'color' => $state->color,
                ],
            ],
            'code' => 200,
        ];
        return response()->json($data);
    }
}
