<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HewanController;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\KonfirmasiPembayaranController;
use App\Http\Controllers\KonsulController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Middleware\HandleCors as MiddlewareHandleCors;


Route::apiResource('hewans', HewanController::class);
Route::apiResource('kliniks', KlinikController::class);
Route::apiResource('konsuls', KonsulController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('messages', MessageController::class);
Route::apiResource('pembayarans', PembayaranController::class);
Route::apiResource('konfirmasipembayarans', KonfirmasiPembayaranController::class);


// User GET,POST,Update
Route::apiResource('users', UserController::class);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


// Routes for Admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [UserController::class, 'adminDashboard']);
    // Access granted message for Admin
    Route::get('/admin/access-granted', function () {
        return response()->json(['message' => 'Access granted to Admin']);
    });
    // Other admin-specific routes can go here
});

// Routes for Dokter
Route::middleware(['auth:sanctum', 'role:dokter'])->group(function () {
    Route::get('/dokter/schedule', [UserController::class, 'dokterSchedule']);
    // Access granted message for Dokter
    Route::get('/dokter/access-granted', function () {
        return response()->json(['message' => 'Access granted to Dokter']);
    });
    // Other doctor-specific routes can go here
});

// Routes for Pasien
Route::middleware(['auth:sanctum', 'role:pasien'])->group(function () {
    Route::get('/pasien/dashboard', [UserController::class, 'pasienDashboard']);
    // Access granted message for Pasien
    Route::get('/pasien/access-granted', function () {
        return response()->json(['message' => 'Access granted to Pasien']);
    });
    // Other patient-specific routes can go here
});







