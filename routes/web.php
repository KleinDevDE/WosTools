<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function() {
    Route::get('/login', [LoginController::class, 'show'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'process']);
    Route::get('/register', [RegisterController::class, 'show'])->name('auth.register');
    Route::post('/register', [RegisterController::class, 'process']);
});

Route::middleware(['auth'])->group(function() {
    Route::get('/', [DashboardController::class, 'show'])->name('dashboard');

    Route::prefix('users')->middleware('can:view users')->group(function() {
        Route::get('/', [UserController::class, 'list'])->name('users.list');
        Route::post('/table', [UserController::class, 'table'])->name('users.table');
    });
});
