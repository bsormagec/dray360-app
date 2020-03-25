<?php

use Illuminate\Http\Request;
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



// API interface for login/signup, etc.
Route::post('login', 'AuthenticationController@login')->name('login');
Route::post('logout', 'AuthenticationController@logout')->name('logout');
Route::post('signup', 'AuthenticationController@signup')->name('signup');


// Authenticated test route to get a user
Route::get('user', 'AuthenticationController@user')
    ->name('user')
    ->middleware('auth:sanctum');


// Authenticated API for returning all orders
Route::middleware('auth:sanctum')->get('/orders', function () {
    $orders = \App\Models\Order::paginate(25);
    return \App\Http\Resources\Orders::collection($orders);
});
