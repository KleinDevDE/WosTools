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
        $characterId = auth('character')->id();

        if (!$characterId) {
            return response()->json(['error' => 'No active character'], 400);
        }

        $puzzles = $album->puzzles()
            ->withCount('pieces')
            ->with(['pieces' => function ($query) use ($characterId) {
                $query->orderBy('position')
                    ->with(['characterStates' => function ($stateQuery) use ($characterId) {
                        $stateQuery->where('character_id', $characterId);
                    }]);
            }])
            ->orderBy('position')
            ->get();

        $puzzles->each(function ($puzzle) {
            $puzzle->completed_pieces = $puzzle->pieces->filter(function ($piece) {
                return $piece->characterStates->first()?->owns || $piece->characterStates->first()?->offers > 0;
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
        $characterId = auth('character')->id();

        if (!$characterId) {
            return response()->json(['error' => 'No active character'], 400);
        }

        $puzzle->load(['pieces' => function ($query) use ($characterId) {
            $query->orderBy('position')
                ->with(['characterStates' => function ($stateQuery) use ($characterId) {
                    $stateQuery->where('character_id', $characterId);
                }]);
        }]);

        $puzzle->completed_pieces = $puzzle->pieces->filter(function ($piece) {
            return $piece->characterStates->first()?->owns === true;
        })->count();

        return response()->json([
            'data' => new PuzzleResource($puzzle),
        ]);
    }
}
