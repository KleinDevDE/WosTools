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
        $userId = auth()->id();

        $states = PuzzlesUserPuzzlePiece::where('user_id', $userId)
            ->where('state', '!=', 'neutral')
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
