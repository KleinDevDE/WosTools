<?php

use Illuminate\Support\Facades\Route;
use Modules\Puzzles\Http\Controllers\PuzzlesController;

Route::middleware(['auth', 'can:'.App\Helpers\Permissions::PUZZLES_VIEW])->group(function () {
    Route::prefix('/puzzles')->name('modules.puzzles.')->group(function () {
        Route::get('/albums', [PuzzlesController::class, 'albums'])
            ->middleware('can:'.App\Helpers\Permissions::PUZZLES_ALBUMS_VIEW)->name('albums');
        Route::get('/puzzles', [PuzzlesController::class, 'puzzles'])
            ->middleware('can:'.App\Helpers\Permissions::PUZZLES_PUZZLES_VIEW)->name('puzzles');
        Route::get('/pieces', [PuzzlesController::class, 'pieces'])
            ->middleware('can:'.App\Helpers\Permissions::PUZZLES_PIECES_VIEW)->name('pieces');
        Route::get('/{any?}', function () {
            return view('puzzles::app');
        })->where('any', '.*')->name('spa');
    });
});
