<?php

namespace App\Http\Controllers;

use App\Models\CollectorLocation;
use App\Models\Collector;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CollectorLocationController extends Controller
{
    /**
     * Actualizar la ubicación actual del recolector
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            $collector = auth()->user()->collector;

            if (!$collector) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no es un recolector',
                ], 403);
            }

            $location = CollectorLocation::create([
                'collector_id' => $collector->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ubicación actualizada correctamente',
                'data' => $location,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la ubicación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener la ubicación actual de un recolector
     */
    public function getCurrentLocation($collectorId): JsonResponse
    {
        try {
            $location = CollectorLocation::where('collector_id', $collectorId)
                ->latest('updated_at')
                ->first();

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ubicación para este recolector',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $location,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la ubicación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener historial de ubicaciones del recolector
     */
    public function getHistory($collectorId, Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 50);

            $locations = CollectorLocation::where('collector_id', $collectorId)
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $locations,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial de ubicaciones',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
