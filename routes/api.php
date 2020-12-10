<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\OCRRulesController;
use App\Http\Controllers\Api\CompaniesController;
use App\Http\Controllers\Api\SendToTmsController;
use App\Http\Controllers\CurrentTenantController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\OCRRequestController;
use App\Http\Controllers\Api\BulkActionsController;
use App\Http\Controllers\Api\OCRVariantsController;
use App\Http\Controllers\Api\UsersStatusController;
use App\Http\Controllers\Api\DivisionCodesController;
use App\Http\Controllers\Api\ImpersonationController;
use App\Http\Controllers\Api\SearchAddressController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\EquipmentTypesController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\OCRRulesAssignmentController;
use App\Http\Controllers\Api\OrderStatusHistoryController;
use App\Http\Controllers\Api\AccesorialCompaniesController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\OcrRequestReprocessController;
use App\Http\Controllers\Api\EquipmentTypesSelectValuesController;
use App\Http\Controllers\Api\DownloadOriginalOrderSourceFileController;

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
    ->middleware('tenant-aware')
    ->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

Route::get('current-tenant', CurrentTenantController::class)
    ->middleware('tenant-aware')
    ->name('current-tenant');

// Sanctum Authenticated Routes
Route::group(['middleware' => ['auth:sanctum', 'impersonate', 'tenant-aware']], function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Authenticated route to get current user
    Route::get('user', [LoginController::class, 'user'])
        ->name('user'); // shows up in `php artisan route:list` command output

    Route::get('roles', RolesController::class)
        ->name('roles.index');

    Route::post('password/change', ChangePasswordController::class)
        ->name('password.change');

    // Users management
    Route::put('users/{user}/status', UsersStatusController::class)
        ->name('users.status.update');
    Route::resource('users', UsersController::class);

    Route::delete('impersonate', [ImpersonationController::class, 'destroy'])
            ->name('impersonate.stop')
            ->withoutMiddleware('impersonate');
    Route::post('impersonate/{user}', [ImpersonationController::class, 'update'])
        ->name('impersonate.start')
        ->withoutMiddleware('impersonate');

    Route::get('search-address', SearchAddressController::class)
        ->name('search-address');

    Route::post('orders/{order}/send-to-tms', SendToTmsController::class)
        ->name('orders.send-to-tms');

    Route::get('orders/{order}/download-source-file', DownloadOriginalOrderSourceFileController::class)
        ->name('orders.download-source-file');

    Route::get('orders/{order}/status-history', OrderStatusHistoryController::class)
        ->name('orders.status-history');

    // Orders management
    Route::apiResource('orders', OrdersController::class)
        ->only(['index', 'update', 'show', 'destroy']);

    // Companies management
    Route::resource('companies', CompaniesController::class)
        ->only(['index', 'update', 'show']);

    //companies/1/tms-provider/1/equipment-types
    Route::get('companies/{company}/tms-provider/{tmsProvider}/equipment-types', EquipmentTypesController::class)
        ->name('equipment-types.show');

    //companies/1/tms-provider/1/equipment-types
    Route::get('companies/{company}/tms-provider/{tmsProvider}/division-names', DivisionCodesController::class)
        ->name('division-names.show');

    //companies/1/tms-provider/1/equipment-types-options
    Route::get('companies/{company}/tms-provider/{tmsProvider}/equipment-types-options', EquipmentTypesSelectValuesController::class)
        ->name('equipment-types-options.show');

    //companies/1/variant/1/
    Route::get('companies/{company}/variants/{variant}/accesorials', [AccesorialCompaniesController::class, 'show'])
        ->name('company-variants-accessorials.show');

    //companies/1/variant/1/
    Route::put('companies/{company}/variants/{variant}/accesorials', [AccesorialCompaniesController::class, 'update'])
        ->name('company-variants-accessorials.put');

    // Reprocess the given OCR request
    Route::post('ocr/requests/{request_id}/reprocess', OcrRequestReprocessController::class)
        ->name('ocr.requests.reprocess');

    // CRUD for OCR Request
    Route::apiResource('ocr/requests', OCRRequestController::class, ['as' => 'ocr'])
        ->parameters(['requests' => 'ocrRequest'])
        ->only(['index', 'store', 'destroy']);

    // CRUD for OCR Rules
    Route::apiResource('ocr/rules', OCRRulesController::class, ['as' => 'ocr'])
        ->parameters(['rules' => 'ocrRule'])
        ->only(['store', 'index', 'update']);

    Route::apiResource('ocr/variants', OCRVariantsController::class, ['as' => 'ocr'])
        ->parameters(['variants' => 'ocrVariant'])
        ->only(['index', 'store', 'update', 'destroy']);

    // Assignment of OCR Rules to an company-variant pair
    Route::apiResource('ocr/rules-assignment', OCRRulesAssignmentController::class, ['as' => 'ocr'])
        ->only(['store', 'index']);

    Route::post('bulk-actions', BulkActionsController::class)
        ->name('bulk-actions');
});
