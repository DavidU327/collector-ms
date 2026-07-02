<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth.jwt')->group(function () {
    Route::post('collector', '\App\Http\Controllers\CollectorStoreController@create')->name('collector.create'); //Crear recolector
    Route::get('collectors', '\App\Http\Controllers\CollectorIndexController@index')->name('collector.index'); //Mostrar recolector
    Route::post('search', '\App\Http\Controllers\ControllerSearchController@search')->name('collector.search'); //Buscar recolector
    Route::get('changeState/{collector}', '\App\Http\Controllers\CollectorChangeStateController@changeState')->name('collector.changeState'); //Cambiar estado
    Route::delete('deleteCollector/{collector}', '\App\Http\Controllers\CollectorDeleteController@delete')->name('user.delete'); //Eliminar usuario

    // HU-18: Tracking del Recolector (Usuario)
    Route::get('my-collector-location', '\App\Http\Controllers\UserOrderTrackingController@getMyCollectorLocation')->name('user.collector.location'); // Ver ubicación del recolector asignado
    Route::get('my-active-orders', '\App\Http\Controllers\UserOrderTrackingController@getActiveOrders')->name('user.active.orders'); // Ver todas las órdenes activas

    // HU-19: Puntos de Recogida (Recolector)
    Route::post('collector/location', '\App\Http\Controllers\CollectorLocationController@update')->name('collector.location.update'); // Actualizar ubicación del recolector
    Route::get('collector/location/history/{collectorId}', '\App\Http\Controllers\CollectorLocationController@getHistory')->name('collector.location.history'); // Historial de ubicaciones
    Route::get('collector/pickup-points', '\App\Http\Controllers\CollectorPickupPointsController@getMyAssignedPickupPoints')->name('collector.pickup.points'); // Obtener puntos de recogida asignados
    Route::patch('pickup-point/{pickupPoint}/complete', '\App\Http\Controllers\CollectorPickupPointsController@completePickupPoint')->name('pickup.point.complete'); // Marcar punto como completado
    Route::get('collector/order/{order}/details', '\App\Http\Controllers\CollectorPickupPointsController@getOrderDetails')->name('collector.order.details'); // Detalles de orden
});
