<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Puzzles\Http\Resources\AlbumResource;
use Modules\Puzzles\Models\PuzzlesAlbum;

class AlbumController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();

        $albums = PuzzlesAlbum::query()
            ->withCount('puzzles')
            ->orderBy('position')
            ->get()
            ->map(function ($album) use ($userId) {
                // Calculate total pieces and completed pieces for this album
                $stats = DB::table('puzzles_album_puzzle_pieces')
                    ->where('puzzles_album_id', $album->id)
                    ->leftJoin('puzzles_user_puzzle_pieces', function ($join) use ($userId) {
                        $join->on('puzzles_album_puzzle_pieces.id', '=', 'puzzles_user_puzzle_pieces.puzzles_album_puzzle_piece_id')
                            ->where('puzzles_user_puzzle_pieces.user_id', '=', $userId);
                    })
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN puzzles_user_puzzle_pieces.offers > 0 THEN 1 ELSE 0 END) as completed')
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
        $userId = auth()->id();

        $album->load(['puzzles' => function ($query) use ($userId) {
            $query->withCount('pieces')
                ->with(['pieces' => function ($pieceQuery) use ($userId) {
                    $pieceQuery->orderBy('position')
                        ->with(['userStates' => function ($stateQuery) use ($userId) {
                            $stateQuery->where('user_id', $userId);
                        }]);
                }]);
        }]);

        // Add user state to each piece
        $album->puzzles->each(function ($puzzle) {
            $puzzle->completed_pieces = $puzzle->pieces->filter(function ($piece) {
                return $piece->userStates->first()?->state === 'have';
            })->count();

            $puzzle->pieces->each(function ($piece) {
                $piece->user_state = $piece->userStates->first()?->state ?? 'neutral';
            });
        });

        return response()->json([
            'data' => new AlbumResource($album),
        ]);
    }
}
