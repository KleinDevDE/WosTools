<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Puzzles\Http\Resources\AlbumResource;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;

class AlbumController extends Controller
{
    public function index(): JsonResponse
    {
        $characterId = auth('character')->id();

        if (!$characterId) {
            return response()->json(['error' => 'No active character'], 400);
        }

        $albums = PuzzlesAlbum::query()
            ->withCount('puzzles')
            ->orderBy('position')
            ->get()
            ->map(function ($album) use ($characterId) {
                // Calculate total pieces and completed pieces for this album
                $stats = DB::table('puzzles_album_puzzle_pieces')
                    ->where('puzzles_album_id', $album->id)
                    ->leftJoin('puzzles_character_puzzle_pieces', function ($join) use ($characterId) {
                        $join->on('puzzles_album_puzzle_pieces.id', '=', 'puzzles_character_puzzle_pieces.puzzles_album_puzzle_piece_id')
                            ->where('puzzles_character_puzzle_pieces.character_id', '=', $characterId);
                    })
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN puzzles_character_puzzle_pieces.offers > 0 OR puzzles_character_puzzle_pieces.owns THEN 1 ELSE 0 END) as completed')
                    ->first();

                $album->total_pieces = $stats->total ?? 0;
                $album->completed_pieces = $stats->completed ?? 0;

                return $album;
            });

        return response()->json([
            'data' => AlbumResource::collection($albums),
            'meta' => [
                'total' => $albums->count(),
                'cached_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function show(PuzzlesAlbum $album): JsonResponse
    {
        $characterId = auth('character')->id();

        if (!$characterId) {
            return response()->json(['error' => 'No active character'], 400);
        }

        $album->load(['puzzles' => function ($query) use ($characterId) {
            $query->withCount('pieces')
                ->with(['pieces' => function ($pieceQuery) use ($characterId) {
                    $pieceQuery->orderBy('position')
                        ->with(['characterStates' => function ($stateQuery) use ($characterId) {
                            $stateQuery->where('character_id', $characterId);
                        }]);
                }]);
        }]);

        // Add character state to each piece
        $album->puzzles->each(function ($puzzle) {
            $puzzle->completed_pieces = $puzzle->pieces->filter(function ($piece) {
                return $piece->characterStates->first()?->owns === true;
            })->count();
        });

        return response()->json([
            'data' => new AlbumResource($album),
        ]);
    }
}
