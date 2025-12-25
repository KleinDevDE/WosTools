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
