<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\TenantController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:api', 'tenant.access'])->group(function () {

        // Auth routes
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Tenant
        Route::get('/tenant', [TenantController::class, 'show']);

        // Projects
        Route::get('/projects/stats', [ProjectController::class, 'stats']);
        Route::apiResource('projects', ProjectController::class);

        // Tasks
        Route::apiResource('tasks', TaskController::class);

        // Users (Admin only)
        Route::middleware('role:owner,admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });
    });
});
