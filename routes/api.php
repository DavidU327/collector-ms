<?php

use \App\Models\Rol;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.jwt', 'role:'.Rol::ADMIN])->group(function () {
    Route::get('collectors', '\App\Http\Controllers\CollectorIndexController@index')->name('collector.index'); //Mostrar recolector
    Route::post('collector-backoffice', '\App\Http\Controllers\CollectorStoreController@createBackOffice')->name('collector.createBackOffice'); //Crear recolector desde el backoffice
    Route::post('upload-document/{collector}', '\App\Http\Controllers\CollectorUploadDocumentController@uploadDocument')->name('collector.uploadDocument'); //Subir documento
    Route::get('states_collector', '\App\Http\Controllers\StateCollectorIndexController@index')->name('index.state'); //Ver estados para el backoffice
    Route::get('change_state_collector/{state}/{collector}', '\App\Http\Controllers\ChangeStateCollectorIndexController@changeState')->name('change.state'); //Cambiar estado en el backoffice
    Route::get('change_state/{collector}', '\App\Http\Controllers\CollectorChangeStateController@changeState')->name('collector.changeState'); //Cambiar estado de habilitado e inhabilitado
    Route::patch('collector/{collector}', '\App\Http\Controllers\CollectorUpdateController@updateBackOffice')->name('collector.update'); //Actualizar recollector
    Route::post('search', '\App\Http\Controllers\ControllerSearchController@search')->name('collector.search'); //Buscar recolector
});


Route::middleware('auth.jwt')->group(function () {

    Route::delete('deleteCollector/{collector}', '\App\Http\Controllers\CollectorDeleteController@delete')->name('user.delete'); //Eliminar usuario
});
