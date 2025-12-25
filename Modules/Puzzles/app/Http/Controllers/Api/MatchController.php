<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Puzzles\Http\Resources\MatchResource;

class MatchController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();

        // Get pieces I need (tradeable only)
        $myNeeds = DB::table('puzzles_user_puzzle_pieces as up')
            ->join('puzzles_album_puzzle_pieces as p', 'up.puzzles_album_puzzle_piece_id', '=', 'p.id')
            ->where('up.user_id', $userId)
            ->where('up.state', 'need')
            ->where('p.stars', '<', 5)
            ->pluck('up.puzzles_album_puzzle_piece_id');

        // Get pieces I have (tradeable only)
        $myHaves = DB::table('puzzles_user_puzzle_pieces as up')
            ->join('puzzles_album_puzzle_pieces as p', 'up.puzzles_album_puzzle_piece_id', '=', 'p.id')
            ->where('up.user_id', $userId)
            ->where('up.state', 'have')
            ->where('p.stars', '<', 5)
            ->pluck('up.puzzles_album_puzzle_piece_id');

        // Find users who have what I need
        $canGetFrom = [];
        if ($myNeeds->isNotEmpty()) {
            $canGetFrom = DB::table('puzzles_user_puzzle_pieces as up')
                ->join('users as u', 'up.user_id', '=', 'u.id')
                ->whereIn('up.puzzles_album_puzzle_piece_id', $myNeeds)
                ->where('up.state', 'have')
                ->where('up.user_id', '!=', $userId)
                ->select('u.id', 'u.username', DB::raw('GROUP_CONCAT(up.puzzles_album_puzzle_piece_id) as piece_ids'))
                ->groupBy('u.id', 'u.username')
                ->get()
                ->map(function ($user) {
                    $pieceIds = explode(',', $user->piece_ids);
                    return [
                        'user' => [
                            'id' => $user->id,
                            'username' => $user->username
                        ],
                        'matching_pieces' => array_map('intval', $pieceIds),
                        'match_count' => count($pieceIds),
                    ];
                })
                ->sortByDesc('match_count')
                ->values()
                ->toArray();
        }

        // Find users who need what I have
        $canHelpWith = [];
        if ($myHaves->isNotEmpty()) {
            $canHelpWith = DB::table('puzzles_user_puzzle_pieces as up')
                ->join('users as u', 'up.user_id', '=', 'u.id')
                ->whereIn('up.puzzles_album_puzzle_piece_id', $myHaves)
                ->where('up.state', 'need')
                ->where('up.user_id', '!=', $userId)
                ->select('u.id', 'u.username', DB::raw('GROUP_CONCAT(up.puzzles_album_puzzle_piece_id) as piece_ids'))
                ->groupBy('u.id', 'u.username')
                ->get()
                ->map(function ($user) {
                    $pieceIds = explode(',', $user->piece_ids);
                    return [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->username,
                        ],
                        'matching_pieces' => array_map('intval', $pieceIds),
                        'match_count' => count($pieceIds),
                    ];
                })
                ->sortByDesc('match_count')
                ->values()
                ->toArray();
        }

        return response()->json([
            'data' => [
                'can_get_from' => $canGetFrom,
                'can_help_with' => $canHelpWith,
            ],
            'meta' => [
                'my_needs_count' => $myNeeds->count(),
                'my_haves_count' => $myHaves->count(),
                'cached_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
