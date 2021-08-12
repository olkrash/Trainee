<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('users')->group(function () {
    Route::post('/', 'App\Http\Controllers\UserController@store');
    Route::post('/login', 'App\Http\Controllers\UserController@login');
    Route::get('/reset_password', 'App\Http\Controllers\UserController@resetPassword');
    Route::put('/change_password', 'App\Http\Controllers\UserController@changePassword');
    Route::get('/', 'App\Http\Controllers\UserController@list');

    Route::middleware(['auth:api'])->group(function () {
        Route::put('/{id}', 'App\Http\Controllers\UserController@update');
        Route::get('/{id}', 'App\Http\Controllers\UserController@view');
    });
});

