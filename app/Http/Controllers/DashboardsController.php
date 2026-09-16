<?php

namespace App\Http\Controllers;

use App\Models\Collector;

class DashboardsController extends Controller
{
    public function allCollectors()
    {
        $collector = Collector::whereNull('deleted_at')
            ->count();
        return response()->json([
            'totalCollector' => $collector,
        ]);
    }

}
