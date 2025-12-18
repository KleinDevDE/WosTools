<?php

use Illuminate\Support\Facades\Route;
use Modules\Puzzles\Http\Controllers\PuzzlesController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('/puzzles')->name('modules.puzzles.')->group(function () {
        Route::get('/', [PuzzlesController::class, 'list'])->name('list');
    });
});
