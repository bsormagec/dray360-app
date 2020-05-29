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

// Sanctum Authenticated Routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Authenticated route to get current user
    Route::get('user', 'AuthenticationController@user')
        ->name('user'); // shows up in `php artisan route:list` command output

    // Authenticated route to return all orders
    Route::get('orders', 'OrderController@orders')
        ->name('orders');

    // Authenticated route to return one order
    Route::get('orders/{orderId}', 'OrderController@order')
        ->name('order');

    // Authenticated route to get document upload URI
    Route::post('createocrrequestuploaduri', 'OCRRequestController@createOCRRequestUploadURI')
        ->name('createocruploaduri');

    // CRUD for OCR Rules
    Route::apiResource('ocr/rules', 'OCRRulesController', ['as' => 'ocr'])
        ->parameters(['rules' => 'ocrRule'])
        ->only(['store', 'index', 'update']);

    // Assignment of OCR Rules to an account-variant pair
    Route::post('ocr/rules-assignment', 'OCRRulesAssignmentController')
        ->name('ocr.rules-assignment.store');
});
