<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Puzzles\Http\Resources\AlbumResource;
use Modules\Puzzles\Http\Resources\PuzzleResource;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return response()->json([
                'data' => [
                    'albums' => [],
                    'puzzles' => [],
                ],
            ]);
        }

        // Search albums
        $albums = PuzzlesAlbum::where('name', 'ilike', "%{$query}%")
            ->orderBy('position')
            ->limit(10)
            ->get();

        // Search puzzles
        $puzzles = PuzzlesAlbumPuzzle::where('name', 'ilike', "%{$query}%")
            ->with('album')
            ->orderBy('position')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'albums' => AlbumResource::collection($albums),
                'puzzles' => PuzzleResource::collection($puzzles),
            ],
            'meta' => [
                'query' => $query,
                'total_results' => $albums->count() + $puzzles->count(),
            ],
        ]);
    }
}
