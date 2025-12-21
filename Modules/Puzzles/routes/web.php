<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('/puzzles')->name('modules.puzzles.')->group(function () {
        Route::get('/{any?}', function () {
            return view('puzzles::app');
        })->where('any', '.*')->name('spa');
    });
});
