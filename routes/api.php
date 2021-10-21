<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\OCRRulesController;
use App\Http\Controllers\Api\AuditLogsController;
use App\Http\Controllers\Api\CompaniesController;
use App\Http\Controllers\Api\FeedbacksController;
use App\Http\Controllers\Api\FieldMapsController;
use App\Http\Controllers\Api\SendToTmsController;
use App\Http\Controllers\CurrentTenantController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\OCRRequestController;
use App\Http\Controllers\Api\BulkActionsController;
use App\Http\Controllers\Api\ObjectLocksController;
use App\Http\Controllers\Api\OCRVariantsController;
use App\Http\Controllers\Api\UsersStatusController;
use App\Http\Controllers\Api\SendToClientController;
use App\Http\Controllers\Api\TmsProvidersController;
use App\Http\Controllers\Api\AbbyyReimportController;
use App\Http\Controllers\Api\DivisionCodesController;
use App\Http\Controllers\Api\ImpersonationController;
use App\Http\Controllers\Api\SearchAddressController;
use App\Http\Controllers\Api\StatusHistoryController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\MetricsReportsController;
use App\Http\Controllers\Api\UploadPtImagesController;
use App\Http\Controllers\Api\DictionaryItemsController;
use App\Http\Controllers\Api\OcrRequestEmailController;
use App\Http\Controllers\Api\ReplicateOrdersController;
use App\Http\Controllers\Api\UpdateAllOrdersController;
use App\Http\Controllers\Api\AuditLogsDashboardController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\FileUploadRequestsController;
use App\Http\Controllers\Api\OCRRulesAssignmentController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\OcrRequestReprocessController;
use App\Http\Controllers\Api\MetricsReportsExportController;
use App\Http\Controllers\Api\RequestStatusUpdatesController;
use App\Http\Controllers\Api\OcrRequestsDoneStatusController;
use App\Http\Controllers\Api\SendRequestOrdersToTmsController;
use App\Http\Controllers\Api\OrderPropertiesAuditLogsController;
use App\Http\Controllers\Api\DownloadOriginalRequestSourceFileController;
use App\Http\Controllers\Api\DownloadOriginalRequestSourceEmailController;

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

Route::post('request-status-updates', [RequestStatusUpdatesController::class, 'store'])
    ->withoutMiddleware(\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class)
    ->name('request-status-updates.store');

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
    Route::apiResource('users', UsersController::class);

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

    Route::post('orders/{order}/send-to-client', SendToClientController::class)
        ->name('orders.send-to-client');

    Route::post('orders/{order}/replicate', ReplicateOrdersController::class)
        ->name('orders.replicate');

    Route::put('orders/{order}/update-all', UpdateAllOrdersController::class)
        ->name('orders.update-all');

    Route::get('status-history', StatusHistoryController::class)
        ->name('status-history.index');

    Route::get('audit-logs', AuditLogsController::class)
        ->name('audit-logs.index');

    Route::get('audit-logs-dashboard', AuditLogsDashboardController::class)
        ->name('audit-logs-dashboard.index');

    Route::get('audit-logs-order-properties', OrderPropertiesAuditLogsController::class)
        ->name('audit-logs-order-properties.index');

    // Orders management
    Route::apiResource('orders', OrdersController::class)
        ->only(['index', 'update', 'show', 'destroy']);

    // Field maps management
    Route::apiResource('field-maps', FieldMapsController::class)
        ->except(['show']);

    // Orders management
    Route::apiResource('dictionary-items', DictionaryItemsController::class)
        ->only(['index', 'store']);

    // Feedbacks management
    Route::apiResource('feedbacks', FeedbacksController::class)
        ->only(['index', 'store']);

    // Companies management
    Route::apiResource('companies', CompaniesController::class)
        ->only(['index', 'update', 'show']);

    // File uploads requests
    Route::post('file-upload-requests', FileUploadRequestsController::class)
        ->name('file-upload-requests.store');

    // Upload pt images
    Route::apiResource('upload-pt-images', UploadPtImagesController::class)
        ->parameters(['upload_pt_images' => 'requestId'])
        ->only(['show', 'store']);

    // Object locks management
    Route::post('object-locks', [ObjectLocksController::class, 'store'])
        ->name('object-locks.store');

    Route::put('object-locks', [ObjectLocksController::class, 'update'])
        ->name('object-locks.update');

    Route::delete('object-locks', [ObjectLocksController::class, 'destroy'])
        ->name('object-locks.destroy');

    Route::get('metrics', MetricsReportsController::class)
        ->name('metrics.index');

    Route::get('metrics-export', MetricsReportsExportController::class)
        ->name('metrics-export.index');

    //companies/1/tms-provider/1/equipment-types
    Route::get('companies/{company}/tms-provider/{tmsProvider}/division-names', DivisionCodesController::class)
        ->name('division-names.show');

    // Send all the request orders to the tms
    Route::post('ocr/requests/{request_id}/send-to-tms', SendRequestOrdersToTmsController::class)
        ->name('ocr.requests.send-to-tms');

    // Reprocess the given OCR request
    Route::post('ocr/requests/{request_id}/reimport-abbyy', AbbyyReimportController::class)
        ->name('ocr.requests.reimport-abbyy');

    // Reprocess the given OCR request
    Route::post('ocr/requests/{request_id}/reprocess', OcrRequestReprocessController::class)
        ->name('ocr.requests.reprocess');

    // Download OCR request source file
    Route::get('ocr/requests/{request_id}/download-source-file', DownloadOriginalRequestSourceFileController::class)
        ->name('ocr.requests.download-source-file');

    // Download OCR request source email
    Route::get('ocr/requests/{request_id}/download-source-email', DownloadOriginalRequestSourceEmailController::class)
        ->name('ocr.requests.download-source-email');

    // Get OCR request source email details
    Route::get('ocr/requests/{request_id}/email-details', OcrRequestEmailController::class)
        ->name('ocr.requests.email-details');

    // Mark OCR request as done/undone
    Route::put('ocr/requests/{request_id}/done-status', OcrRequestsDoneStatusController::class)
        ->name('ocr.requests.done-status');

    // CRUD for OCR Request
    Route::apiResource('ocr/requests', OCRRequestController::class, ['as' => 'ocr'])
        ->parameters(['requests' => 'ocrRequest'])
        ->only(['index', 'store', 'destroy']);

    // Tms providers
    Route::apiResource('tms-providers', TmsProvidersController::class)
        ->only(['index']);

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
