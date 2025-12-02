<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Blogs\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register'])->name('registerPost');
Route::post('/login', [AuthController::class, 'login'])->name('loginPost');
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
});



// Route::middleware('auth:api')->group(function () {
//     Route::get('/blogs', [BlogController::class, 'index'])->name('api.blogs.index');
//     Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('api.blogs.show');
//     Route::post('/blogs', [BlogController::class, 'store'])->name('api.blogs.store');
//     Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('api.blogs.update');
//     Route::delete('/blogs/{slug}', [BlogController::class, 'destroy'])->name('api.blogs.destroy');
// });

    Route::get('/blogs', [BlogController::class, 'index'])->name('api.blogs.index');
    Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('api.blogs.show');
    Route::post('/blogs', [BlogController::class, 'store'])->name('api.blogs.store');
    Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('api.blogs.update');
    Route::delete('/blogs/{slug}', [BlogController::class, 'destroy'])->name('api.blogs.destroy');
