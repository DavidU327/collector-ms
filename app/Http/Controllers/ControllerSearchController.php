<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectorSearchRequest;
use App\Http\Resources\CollectorResource;
use App\Models\Collector;


class ControllerSearchController extends Controller
{
    public function search(CollectorSearchRequest $collectorSearchRequest)
    {
        $searchTerm = $collectorSearchRequest->search;

        $collectors = Collector::with('user')
        ->whereHas('user', function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email', 'LIKE', "%{$searchTerm}%");
        })
            ->whereNull('deleted_at')
            ->paginate(10); // Paginar correctamente

        return CollectorResource::collection($collectors);
    }
}
