<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Puzzles\Http\Resources\PieceResource;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;
use Modules\Puzzles\Models\PuzzlesUserPuzzlePiece;

class PieceController extends Controller
{
    public function updateState(Request $request, PuzzlesAlbumPuzzlePiece $piece): JsonResponse
    {
        $validated = $request->validate([
            'needs' => ['required', 'boolean'],
            'owns' => ['required', 'boolean'],
            'offers' => ['required', 'integer', 'min:0'],
        ]);

        $characterId = auth()->user()->activeCharacter()?->id;

        if (!$characterId) {
            return response()->json(['error' => 'No active character'], 400);
        }

        // Check if piece is tradeable (5+ stars cannot be traded)
        if (!$piece->isTradeable() && ($validated['needs'] || $validated['offers'] > 0)) {
            return response()->json([
                'error' => 'This piece cannot be traded (5+ stars)',
            ], 422);
        }

        // Update or create character piece state
        $characterPiece = PuzzlesUserPuzzlePiece::updateOrCreate(
            [
                'character_id' => $characterId,
                'puzzles_album_puzzle_piece_id' => $piece->id,
            ],
            [
                'needs' => $validated['needs'],
                'owns' => $validated['owns'],
                'offers' => $validated['offers'],
            ]
        );

        return response()->json([
            'data' => new PieceResource($piece),
            'message' => 'Piece state updated successfully',
        ]);
    }
}
