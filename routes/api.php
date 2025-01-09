<?php

use Illuminate\Support\Facades\Route;

//Users
Route::prefix('users')->group(function () {
    Route::get('users', '\App\Http\Controllers\UserIndexController@index')->name('user.index');
    Route::post('user/{user}', '\App\Http\Controllers\UserUpdateController@update')->name('user.update');
});
