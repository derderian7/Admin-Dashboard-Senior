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

Route::post('/login', [AuthController::class, 'login']);

Route::post('/user_login', [AuthController::class, 'user_login']);

Route::apiresource('services', ServiceController::class);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/generate_script', function (){
        return response()->json("<script>
        var iframe = document.createElement('iframe');
        iframe.setAttribute('width', '800px');
        iframe.setAttribute('height', '800px');
        iframe.setAttribute('src', 'http://localhost:7200');
        iframe.style.border = '2px solid'; 
        document.body.appendChild(iframe);
    </script>");
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin_change_password', [UserController::class, 'admin_change_password']);
    Route::get('/users_count', [UserController::class, 'users_count']);
    Route::apiresource('users', UserController::class);
    Route::get('user_services', [UserController::class, 'user_services']);
    Route::get('/services_count', [ServiceController::class, 'services_count']);
    Route::get('/famous_services', [ServiceController::class, 'famous_services']);
    Route::get('services_total_usage', [ServiceController::class, 'total_usage']);
});
