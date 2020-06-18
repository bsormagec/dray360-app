<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\OCRRulesController;
use App\Http\Controllers\Api\SendToTmsController;
use App\Http\Controllers\Api\OCRRequestController;
use App\Http\Controllers\Api\SearchAddressController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\OCRRulesAssignmentController;

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
Route::post('login', [AuthenticationController::class, 'login'])->name('login');
Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
Route::post('signup', [AuthenticationController::class, 'signup'])->name('signup');

// Sanctum Authenticated Routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    // Authenticated route to get current user
    Route::get('user', [AuthenticationController::class, 'user'])
        ->name('user'); // shows up in `php artisan route:list` command output

    Route::get('search-address', SearchAddressController::class)
        ->name('search-address.index');

    Route::post('send-to-tms', SendToTmsController::class)
        ->name('sent-to-tms.store');

    // Authenticated route to return all orders
    Route::resource('orders', OrdersController::class)
        ->only(['index', 'update', 'show']);

    // Authenticated route to get document upload URI
    Route::post('createocrrequestuploaduri', [OCRRequestController::class, 'createOCRRequestUploadURI'])
        ->name('createocruploaduri');

    // CRUD for OCR Rules
    Route::apiResource('ocr/rules', OCRRulesController::class, ['as' => 'ocr'])
        ->parameters(['rules' => 'ocrRule'])
        ->only(['store', 'index', 'update']);

    // Assignment of OCR Rules to an account-variant pair
    Route::apiResource('ocr/rules-assignment', OCRRulesAssignmentController::class, ['as' => 'ocr'])
        ->only(['store', 'index']);
});
