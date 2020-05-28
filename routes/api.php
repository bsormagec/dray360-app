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



// Non-authenticated routes for signup/login/logout
Route::post('login', 'AuthenticationController@login')->name('login');
Route::post('logout', 'AuthenticationController@logout')->name('logout');
Route::post('signup', 'AuthenticationController@signup')->name('signup');


// Authenticated route to get current user
Route::get('user', 'AuthenticationController@user')
    ->name('user') // shows up in `php artisan route:list` command output
    ->middleware('auth:sanctum');


// Authenticated route to return all orders
Route::get('orders', 'OrderController@orders')
    ->name('orders')
    ->middleware('auth:sanctum');

// Authenticated route to return one order
Route::get('orders/{orderId}', 'OrderController@order')
    ->name('order')
    ->middleware('auth:sanctum');


// Authenticated route to get document upload URI
Route::post('createocrrequestuploaduri', 'OCRRequestController@createOCRRequestUploadURI')
    ->name('createocruploaduri')
    ->middleware('auth:sanctum');

Route::apiResource('ocr/rules', 'OCRRulesController', ['as' => 'ocr'])
    ->parameters(['rules' => 'ocrRule'])
    ->only(['store', 'index', 'update'])
    ->middleware('auth:sanctum');
