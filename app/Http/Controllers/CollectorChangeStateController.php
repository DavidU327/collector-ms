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
        $data = [
            'message' => 'Recolector actualizado',
            'order' => CollectorResource::make($collector),
            'code' => 200,
        ];
        return response()->json($data);
    }
}
