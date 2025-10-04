<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollectorResource;
use App\Models\Collector;

class CollectorIndexController extends Controller
{
    public function index()
    {
        $collectors = Collector::whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return CollectorResource::collection($collectors);
    }
}
