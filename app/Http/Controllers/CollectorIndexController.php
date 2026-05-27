<?php

namespace App\Http\Controllers;

use App\Http\Resources\CollectorResource;
use App\Http\Resources\CollectorDashboardResource;
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

    public function indexDashboard()
    {
        $collectors = Collector::whereNull('deleted_at')
            ->where('state_id', 1)
            ->orderBy('id', 'DESC')
            ->get();
        return CollectorDashboardResource::collection($collectors);
    }
}
