<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('verify', [App\Http\Controllers\Auth\RegisterController::class, 'verify']);
    Route::post('verify/resend', [App\Http\Controllers\Auth\RegisterController::class, 'resend']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/profile', [UserController::class, 'index']);
});
