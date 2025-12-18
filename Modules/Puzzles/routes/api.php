<?php

use Illuminate\Support\Facades\Route;
use Modules\Puzzles\Http\Controllers\PuzzlesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('puzzles', PuzzlesController::class)->names('puzzles');
});
