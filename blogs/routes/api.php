<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group([
    'prefix' => 'auth',
], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('loginPost');
    Route::post('register', [AuthController::class, 'register'])->name('registerPost');
    Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');

});





// Route::middleware('auth:api')->group(function () {
//     Route::post('me', [AuthController::class, 'me']);
//     Route::post('logout', [AuthController::class, 'logout'])->name('logout');
//     Route::post('refresh', [AuthController::class, 'refresh']);
// });
