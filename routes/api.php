<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Module API routes are loaded automatically by Nwidart Modules
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/api/me', function () {
        return [
            'id' => auth()->id(),
            'name' => auth()->user()->username,
            'roles' => auth()->user()->roles->pluck('name'),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
        ];
    });
});
