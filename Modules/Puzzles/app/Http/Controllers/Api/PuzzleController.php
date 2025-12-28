<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Puzzles\Http\Resources\PuzzleResource;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;

class PuzzleController extends Controller
{
    public function index(PuzzlesAlbum $album): JsonResponse
    {
        $userId = auth()->id();

        $puzzles = $album->puzzles()
            ->withCount('pieces')
            ->with(['pieces' => function ($query) use ($userId) {
                $query->orderBy('position')
                    ->with(['userStates' => function ($stateQuery) use ($userId) {
                        $stateQuery->where('user_id', $userId);
                    }]);
            }])
            ->orderBy('position')
            ->get();

        $puzzles->each(function ($puzzle) {
            $puzzle->completed_pieces = $puzzle->pieces->filter(function ($piece) {
                return $piece->userStates->first()?->owns || $piece->userStates->first()?->offers > 0;
            })->count();
        });

        return response()->json([
            'data' => PuzzleResource::collection($puzzles),
            'meta' => [
                'total' => $puzzles->count(),
            ],
        ]);
    }

    public function show(PuzzlesAlbumPuzzle $puzzle): JsonResponse
    {
        $userId = auth()->id();

        $puzzle->load(['pieces' => function ($query) use ($userId) {
            $query->orderBy('position')
                ->with(['userStates' => function ($stateQuery) use ($userId) {
                    $stateQuery->where('user_id', $userId);
                }]);
        }]);

        $puzzle->completed_pieces = $puzzle->pieces->filter(function ($piece) {
            return $piece->user_state === 'have';
        })->count();

        return response()->json([
            'data' => new PuzzleResource($puzzle),
        ]);
    }
}
