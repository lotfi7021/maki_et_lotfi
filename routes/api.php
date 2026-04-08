<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Routes utilisateur connecte
Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::get('me',       [AuthController::class, 'me']);
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Routes admin seulement
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('users',           [AdminController::class, 'listUsers']);
    Route::delete('users/{id}',   [AdminController::class, 'deleteUser']);
    Route::put('users/{id}/role', [AdminController::class, 'changeRole']);
});