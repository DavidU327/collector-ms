<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Http\Resources\StateResource;

class StateCollectorIndexController extends Controller
{
    public function index()
    {
        $state = StateResource::collection(State::whereIn('name', [State::ENABLED, State::REJECT_USER])->get());
        return [
            'message' => 'Estados',
            'state' => $state,
            'code' => 200,
        ];
    }
}
