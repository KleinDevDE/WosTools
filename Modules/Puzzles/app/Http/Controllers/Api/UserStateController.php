<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Puzzles\Http\Resources\UserStateResource;
use Modules\Puzzles\Models\PuzzlesUserPuzzlePiece;

class UserStateController extends Controller
{
    public function index(): JsonResponse
    {
        $characterId = auth()->user()->activeCharacter()?->id;

        if (!$characterId) {
            return response()->json(['error' => 'No active character'], 400);
        }

        $states = PuzzlesUserPuzzlePiece::where('character_id', $characterId)
            ->where(function ($query) {
                $query->where('needs', true)
                    ->orWhere('owns', true)
                    ->orWhere('offers', '>', 0);
            })
            ->get();

        return response()->json([
            'data' => UserStateResource::collection($states),
            'meta' => [
                'total' => $states->count(),
                'cached_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
