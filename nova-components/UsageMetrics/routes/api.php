<?php

use Illuminate\Support\Facades\Route;
use Dray360\UsageMetrics\Http\Controllers\UsageMetricsController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/companies/{company}', UsageMetricsController::class);
