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
            'state' => ['required', Rule::in(['neutral', 'need', 'have'])],
        ]);

        $userId = auth()->id();

        // Check if piece is tradeable (5+ stars cannot be traded)
        if (!$piece->isTradeable() && in_array($validated['state'], ['need', 'have'])) {
            return response()->json([
                'error' => 'This piece cannot be traded (5+ stars)',
            ], 422);
        }

        // Update or create user piece state
        $userPiece = PuzzlesUserPuzzlePiece::updateOrCreate(
            [
                'user_id' => $userId,
                'puzzles_album_puzzle_piece_id' => $piece->id,
            ],
            [
                'state' => $validated['state'],
            ]
        );

        $piece->user_state = $userPiece->state;

        return response()->json([
            'data' => new PieceResource($piece),
            'message' => 'Piece state updated successfully',
        ]);
    }
}
