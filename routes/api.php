<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/users_count', [UserController::class, 'users_count']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users_count', [UserController::class, 'users_count']);
    Route::apiresource('users', UserController::class);
    Route::get('/services_count', [ServiceController::class, 'services_count']);
    Route::apiresource('services', ServiceController::class);
    Route::get('/famous_services', [ServiceController::class, 'famous_services']);
});
