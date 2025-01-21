<?php

use Illuminate\Support\Facades\Route;

//Users
Route::prefix('collector')->group(function () {
    Route::post('collector', '\App\Http\Controllers\CollectorStoreController@create')->name('collector.create'); //Crear recolector
});
