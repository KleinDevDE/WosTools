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

        $userId = auth()->id();

        // Check if piece is tradeable (5+ stars cannot be traded)
        if (!$piece->isTradeable() && ($validated['needs'] || $validated['offers'] > 0)) {
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

    public function bulkUpdateState(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pieces' => ['required', 'array', 'min:1'],
            'pieces.*.piece_id' => ['required', 'integer', 'exists:puzzles_album_puzzle_pieces,id'],
            'pieces.*.needs' => ['required', 'boolean'],
            'pieces.*.owns' => ['required', 'boolean'],
            'pieces.*.offers' => ['required', 'integer', 'min:0'],
        ]);

        $userId = auth()->id();
        $updatedCount = 0;
        $errors = [];

        foreach ($validated['pieces'] as $pieceData) {
            $piece = PuzzlesAlbumPuzzlePiece::find($pieceData['piece_id']);

            // Check if piece is tradeable (5+ stars cannot be traded)
            if (!$piece->isTradeable() && ($pieceData['needs'] || $pieceData['offers'] > 0)) {
                $errors[] = [
                    'piece_id' => $piece->id,
                    'error' => 'This piece cannot be traded (5+ stars)',
                ];
                continue;
            }

            // Update or create user piece state
            PuzzlesUserPuzzlePiece::updateOrCreate(
                [
                    'user_id' => $userId,
                    'puzzles_album_puzzle_piece_id' => $piece->id,
                ],
                [
                    'needs' => $pieceData['needs'],
                    'owns' => $pieceData['owns'],
                    'offers' => $pieceData['offers'],
                ]
            );

            $updatedCount++;
        }

        return response()->json([
            'message' => 'Bulk update completed',
            'updated_count' => $updatedCount,
            'errors' => $errors,
        ]);
    }
}
