<?php

use App\Http\Controllers\About\AboutController;
use App\Http\Controllers\Blogs\BlogController;
use App\Http\Controllers\Contact\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');


Route::get('/login', function(){
    return view('pages.auth.login');
})->name('login');

Route::get('/register', function(){
    return view('pages.auth.register');
})->name('register');

// BLOG ROUTES
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');
Route::get('/blogs/{slug}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('blogs.update');
Route::delete('/blogs/{slug}', [BlogController::class, 'destroy'])->name('blogs.destroy');

Route::get('/about', [AboutController::class, 'index']);
Route::get('/contact', [ContactController::class, 'index']);
