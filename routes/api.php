<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\OCRRulesController;
use App\Http\Controllers\Api\SendToTmsController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\OCRRequestController;
use App\Http\Controllers\Api\OCRVariantsController;
use App\Http\Controllers\Api\SearchAddressController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\OCRRulesAssignmentController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\DownloadOriginalOrderPdfController;

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
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('signup', [LoginController::class, 'signup'])->name('signup');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Sanctum Authenticated Routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Authenticated route to get current user
    Route::get('user', [LoginController::class, 'user'])
        ->name('user'); // shows up in `php artisan route:list` command output

    Route::get('search-address', SearchAddressController::class)
        ->name('search-address');

    Route::post('send-to-tms', SendToTmsController::class)
        ->name('send-to-tms');

    Route::get('orders/{order}/download-pdf', DownloadOriginalOrderPdfController::class)
        ->name('orders.download-pdf');

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

    Route::apiResource('ocr/variants', OCRVariantsController::class, ['as' => 'ocr'])
        ->parameters(['variants' => 'ocrVariant'])
        ->only(['index', 'store', 'update', 'destroy']);

    // Assignment of OCR Rules to an account-variant pair
    Route::apiResource('ocr/rules-assignment', OCRRulesAssignmentController::class, ['as' => 'ocr'])
        ->only(['store', 'index']);
});
