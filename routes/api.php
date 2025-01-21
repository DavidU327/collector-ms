<?php

use Illuminate\Support\Facades\Route;

//Users
Route::prefix('users')->group(function () {
    Route::get('users', '\App\Http\Controllers\UserIndexController@index')->name('user.index'); //Buscar usuarios paginados
    Route::post('user/{user}', '\App\Http\Controllers\UserUpdateController@update')->name('user.update'); //Actualizar usuario
    Route::post('search', '\App\Http\Controllers\UserSearchController@search')->name('user.search'); //Buscar usuario
    Route::get('changeState/{user}', '\App\Http\Controllers\UserChangeStateController@changeState')->name('user.changeState'); //Cambiar estado
    Route::delete('deleteUser/{user}', '\App\Http\Controllers\UserDeleteController@delete')->name('user.delete'); //Eliminar usuario
});
