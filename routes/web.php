<?php

use App\Http\Controllers\About\AboutController;
use App\Http\Controllers\Blogs\BlogController;
use App\Http\Controllers\Contact\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::get('/about', [AboutController::class, 'index']);
Route::get('/contact', [ContactController::class, 'index']);

Route::view('/login', 'pages.auth.login')->name('login');
Route::view('/register', 'pages.auth.register')->name('register');
Route::view('/verify-email', 'pages.auth.verify-email')->name('verify.email.page');

Route::view('blogs','pages.blogs.index')->name('blogs.index');
// BLOG ROUTES
Route::middleware('auth:api')->group(function () {
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
    Route::get('/blogs/{slug}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
});
