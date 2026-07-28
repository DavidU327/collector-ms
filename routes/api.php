<?php

use \App\Models\Rol;
use Illuminate\Support\Facades\Route;

Route::get('collectors', '\App\Http\Controllers\CollectorIndexController@index')->name('collector.index'); //Mostrar recolector

Route::middleware(['auth.jwt', 'role:'.Rol::ADMIN])->group(function () {
    Route::get('collectors-dashboard', '\App\Http\Controllers\CollectorIndexController@indexDashboard')->name('collector.indexDashboard'); //Mostrar recolector para dashboard
    Route::post('collector-backoffice', '\App\Http\Controllers\CollectorStoreController@createBackOffice')->name('collector.createBackOffice'); //Crear recolector desde el backoffice
    Route::post('upload-document/{collector}', '\App\Http\Controllers\CollectorUploadDocumentController@uploadDocument')->name('collector.uploadDocument'); //Subir documento
    Route::get('states_collector', '\App\Http\Controllers\StateCollectorIndexController@index')->name('index.state'); //Ver estados para el backoffice
    Route::get('change_state_collector/{state}/{collector}', '\App\Http\Controllers\ChangeStateCollectorIndexController@changeState')->name('change.state'); //Cambiar estado en el backoffice
    Route::get('change_state/{collector}', '\App\Http\Controllers\CollectorChangeStateController@changeState')->name('collector.changeState'); //Cambiar estado de habilitado e inhabilitado
    Route::patch('collector/{collector}', '\App\Http\Controllers\CollectorUpdateController@updateBackOffice')->name('collector.update'); //Actualizar recollector
    Route::post('search', '\App\Http\Controllers\ControllerSearchController@search')->name('collector.search'); //Buscar recolector
    Route::delete('deleteCollector/{collector}', '\App\Http\Controllers\CollectorDeleteController@delete')->name('collector.delete'); //Eliminar usuario
});


Route::middleware('auth.jwt')->group(function () {
    // HU-18: Tracking del Recolector (Usuario)
    Route::get('my-collector-location', '\App\Http\Controllers\UserOrderTrackingController@getMyCollectorLocation')->name('user.collector.location'); // Ver ubicación del recolector asignado
    Route::get('my-active-orders', '\App\Http\Controllers\UserOrderTrackingController@getActiveOrders')->name('user.active.orders'); // Ver todas las órdenes activas

    // HU-19: Puntos de Recogida (Recolector)
    Route::post('collector/location', '\App\Http\Controllers\CollectorLocationController@update')->name('collector.location.update'); // Actualizar ubicación del recolector
    Route::get('collector/location/history/{collectorId}', '\App\Http\Controllers\CollectorLocationController@getHistory')->name('collector.location.history'); // Historial de ubicaciones
    Route::get('collector/pickup-points', '\App\Http\Controllers\CollectorPickupPointsController@getMyAssignedPickupPoints')->name('collector.pickup.points'); // Obtener puntos de recogida asignados
    Route::patch('pickup-point/{pickupPoint}/complete', '\App\Http\Controllers\CollectorPickupPointsController@completePickupPoint')->name('pickup.point.complete'); // Marcar punto como completado
    Route::get('collector/order/{order}/details', '\App\Http\Controllers\CollectorPickupPointsController@getOrderDetails')->name('collector.order.details'); // Detalles de orden
    Route::get('collector/{collector}', '\App\Http\Controllers\CollectorIndexController@show')->name('collector.show'); //Ver información del recolector
});

