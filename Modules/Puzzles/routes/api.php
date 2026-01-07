<?php

use Illuminate\Support\Facades\Route;
use Modules\Puzzles\Http\Controllers\Api\AlbumController;
use Modules\Puzzles\Http\Controllers\Api\PuzzleController;
use Modules\Puzzles\Http\Controllers\Api\PieceController;
use Modules\Puzzles\Http\Controllers\Api\UserStateController;
use Modules\Puzzles\Http\Controllers\Api\MatchController;
use Modules\Puzzles\Http\Controllers\Api\SearchController;

Route::middleware(['auth:sanctum'])->prefix('v1/puzzles')->name('puzzles.')->group(function () {
    // Albums
    Route::get('albums', [AlbumController::class, 'index'])->name('albums.index');
    Route::get('albums/{album}', [AlbumController::class, 'show'])->name('albums.show');

    // Puzzles
    Route::get('albums/{album}/puzzles', [PuzzleController::class, 'index'])->name('albums.puzzles.index');
    Route::get('puzzles/{puzzle}', [PuzzleController::class, 'show'])->name('puzzles.show');

    // Piece state management
    Route::post('pieces/{piece}/state', [PieceController::class, 'updateState'])->name('pieces.state');
    Route::post('pieces/bulk-update', [PieceController::class, 'bulkUpdateState'])->name('pieces.bulk-update');

    // User states (bulk)
    Route::get('user/states', [UserStateController::class, 'index'])->name('user.states');

    // Matches
    Route::get('matches', [MatchController::class, 'index'])->name('matches.index');

    // Search
    Route::get('search', [SearchController::class, 'index'])->name('search');
});
