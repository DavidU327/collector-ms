<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Users
Route::prefix('users')->group(function () {
    Route::get('users', '\App\Http\Controllers\UserIndexController@index')->name('user.index');
});
