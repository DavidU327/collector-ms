<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth.jwt')->group(function () {
    Route::post('collector', '\App\Http\Controllers\CollectorStoreController@create')->name('collector.create'); //Crear recolector
    Route::get('collectors', '\App\Http\Controllers\CollectorIndexController@index')->name('collector.index'); //Mostrar recolector
    Route::post('search', '\App\Http\Controllers\ControllerSearchController@search')->name('collector.search'); //Buscar recolector
    Route::get('changeState/{collector}', '\App\Http\Controllers\CollectorChangeStateController@changeState')->name('collector.changeState'); //Cambiar estado
    Route::delete('deleteCollector/{collector}', '\App\Http\Controllers\CollectorDeleteController@delete')->name('user.delete'); //Eliminar usuario
});
