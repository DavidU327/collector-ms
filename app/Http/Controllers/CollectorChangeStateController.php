<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollectorResource;
use App\Models\Collector;
use App\Models\State;

class CollectorChangeStateController extends Controller
{
    public function getState($stateId)
    {
        $state = State::find($stateId);
        return $state->name == State::DISABLED ?
            State::where('name', State::ENABLED)->value('id') :
            State::where('name', State::DISABLED)->value('id');
    }

    public function changeState (Collector $collector) {

        $state = $this->getState($collector->state_id);
        $collector->state_id = $state;
        $collector->save();
        $infoState = State::find($state);
        $data = [
            'message' => 'Cambio de estado correctamente',
            'data' => [
                'id' => $collector->id,
                'state' => [
                    'id' => $infoState->id,
                    'name' => $infoState->name,
                    'color' => $infoState->color,
                ],
            ],
            'code' => 200,
        ];
        return response()->json($data);
    }
}
