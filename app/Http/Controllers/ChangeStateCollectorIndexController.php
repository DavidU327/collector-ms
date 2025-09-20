<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use App\Models\State;
use App\Http\Resources\StateResource;

class ChangeStateCollectorIndexController extends Controller
{
    public function changeState(State $state, Collector $collector)
    {
        $collector->state_id = $state->id;
        $collector->save();
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
