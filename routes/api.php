<?php

use Illuminate\Support\Facades\Route;

//Users
Route::prefix('collectors')->group(function () {
    Route::post('collector', '\App\Http\Controllers\CollectorStoreController@create')->name('collector.create'); //Crear recolector
    Route::get('collectors', '\App\Http\Controllers\CollectorIndexController@index')->name('index.create'); //Mostrar recolector
});
