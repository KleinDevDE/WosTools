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
            'player_id' => auth()->user()->player_id,
            'name' => auth()->user()->getName(),
            'display_name' => auth()->user()->getName(),
            'roles' => auth()->user()->roles->pluck('name'),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
        ];
    });

    Route::get('/player/{playerId}', function (int $playerId) {
        $apiService = new \App\Services\WhiteoutSurvivalApiService();
        $playerStats = $apiService->getPlayerStats($playerId);

        if (!$playerStats) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $playerStats->toArray()
        ]);
    });
});
