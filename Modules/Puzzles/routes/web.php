<?php

use Illuminate\Support\Facades\Route;
use Modules\Puzzles\Http\Controllers\PuzzlesController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('/puzzles')->name('modules.puzzles.')->group(function () {
        Route::get('/list', [PuzzlesController::class, 'list'])->name('list');
        Route::get('/{any?}', function () {
            return view('puzzles::app');
        })->where('any', '.*')->name('spa');
    });
});
