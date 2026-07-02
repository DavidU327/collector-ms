<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PickupPoint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserOrderTrackingController extends Controller
{
    /**
     * HU-18: Obtener el recolector asignado y su ubicación actual
     */
    public function getMyCollectorLocation(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Obtener la orden activa/más reciente del usuario
            $order = Order::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->orWhere('completed_at', '>', now()->subHours(24))
                ->latest('created_at')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay recolector asignado en este momento',
                ], 404);
            }

            if (!$order->collector) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aún no se ha asignado un recolector',
                ], 404);
            }

            // Obtener la ubicación actual del recolector
            $collectorLocation = $order->collector->currentLocation;

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'collector' => [
                        'id' => $order->collector->id,
                        'name' => $order->collector->user->name ?? 'Recolector',
                        'location' => $collectorLocation ? [
                            'latitude' => $collectorLocation->latitude,
                            'longitude' => $collectorLocation->longitude,
                            'updated_at' => $collectorLocation->updated_at,
                        ] : null,
                    ],
                    'pickup_location' => [
                        'latitude' => $order->latitude,
                        'longitude' => $order->longitude,
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la ubicación del recolector',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener todas las órdenes activas del usuario con ubicación del recolector
     */
    public function getActiveOrders(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            $orders = Order::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->with(['collector.currentLocation', 'pickupPoints'])
                ->get()
                ->map(fn($order) => [
                    'id' => $order->id,
                    'collector' => $order->collector ? [
                        'id' => $order->collector->id,
                        'name' => $order->collector->user->name ?? 'Recolector',
                        'location' => $order->collector->currentLocation ? [
                            'latitude' => $order->collector->currentLocation->latitude,
                            'longitude' => $order->collector->currentLocation->longitude,
                            'updated_at' => $order->collector->currentLocation->updated_at,
                        ] : null,
                    ] : null,
                    'pickup_location' => [
                        'latitude' => $order->latitude,
                        'longitude' => $order->longitude,
                    ],
                    'status' => $order->state->name ?? 'pending',
                ]);

            return response()->json([
                'success' => true,
                'data' => $orders,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las órdenes activas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
