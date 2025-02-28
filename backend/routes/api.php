<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Маршруты для авторизации (не требуют токена)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Маршруты API для пользователей (требуют авторизации)
Route::middleware('auth:sanctum')->apiResource('users', UserController::class);

// Маршрут для получения данных текущего пользователя (защищён токеном)
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getCurrentUser']);

// Маршруты API для отделов (требуют авторизации)
Route::middleware('auth:sanctum')->apiResource('departments', DepartmentController::class);

// Маршруты API для посетителей (требуют авторизации)
Route::middleware('auth:sanctum')->apiResource('visitors', VisitorController::class);

// Маршруты API для посещений (требуют авторизации)
Route::middleware('auth:sanctum')->apiResource('visits', VisitController::class);

// Маршруты для дашборда (требуют авторизации)
Route::middleware('auth:sanctum')->group(function() {
    Route::get('dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('dashboard/best-performers', [DashboardController::class, 'bestPerformers']);
    Route::get('dashboard/graph', [DashboardController::class, 'graphData']);
});
