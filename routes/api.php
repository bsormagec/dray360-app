<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\AuthenticationController; //pbn
// use App\Http\Controllers\API\AuthenticationController;


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

// needed?
Auth::routes();


// API interface for login/signup, etc.
Route::post('login', 'AuthenticationController@login')->name('login');
Route::post('logout', 'AuthenticationController@logout')->name('logout');
Route::post('signup', 'AuthenticationController@signup')->name('signup');
Route::post('user', 'AuthenticationController@user')->name('user');


// API for returning all orders, unauthenticated
Route::get('/orders', function () {
    $orders = \App\Models\Order::paginate(25);
    return \App\Http\Resources\Orders::collection($orders);
});


// test route to get a user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// asdf
Route::middleware('auth:sanctum')->post('/orders', function () {
    $orders = \App\Models\Order::paginate(25);
    return \App\Http\Resources\Orders::collection($orders);
});
