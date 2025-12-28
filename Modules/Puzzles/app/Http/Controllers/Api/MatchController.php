<?php

namespace Modules\Puzzles\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Puzzles\Http\Resources\MatchResource;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;

class MatchController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $locale = app()->getLocale();

        // Get pieces I need (tradeable only)
        $myNeeds = DB::table('puzzles_user_puzzle_pieces as up')
            ->join('puzzles_album_puzzle_pieces as p', 'up.puzzles_album_puzzle_piece_id', '=', 'p.id')
            ->where('up.user_id', $userId)
            ->where('up.needs', true)
            ->where('p.stars', '<', 5)
            ->pluck('up.puzzles_album_puzzle_piece_id');

        // Get pieces I'm offering (tradeable only)
        $myOffers = DB::table('puzzles_user_puzzle_pieces as up')
            ->join('puzzles_album_puzzle_pieces as p', 'up.puzzles_album_puzzle_piece_id', '=', 'p.id')
            ->where('up.user_id', $userId)
            ->where('up.offers', '>', 0)
            ->where('p.stars', '<', 5)
            ->pluck('up.puzzles_album_puzzle_piece_id');

        // Find pieces that other users are offering that I need
        $canGetFrom = [];
        if ($myNeeds->isNotEmpty()) {
            $canGetFrom = DB::table('puzzles_user_puzzle_pieces as up')
                ->join('users as u', 'up.user_id', '=', 'u.id')
                ->join('puzzles_album_puzzle_pieces as p', 'up.puzzles_album_puzzle_piece_id', '=', 'p.id')
                ->join('puzzles_album_puzzles as puzzle', 'p.puzzles_album_puzzle_id', '=', 'puzzle.id')
                ->join('puzzles_albums as album', 'p.puzzles_album_id', '=', 'album.id')
                ->whereIn('up.puzzles_album_puzzle_piece_id', $myNeeds)
                ->where('up.offers', '>', 0)
                ->where('up.user_id', '!=', $userId)
                ->select(
                    'p.id as piece_id',
                    'p.position',
                    'p.stars',
                    'puzzle.id as puzzle_id',
                    "puzzle.name->$locale as puzzle_name",
                    'album.id as album_id',
                    "album.name->$locale as album_name",
                    'u.id as user_id',
                    'u.username',
                    'u.display_name',
                    'up.offers',
                    'up.updated_at as last_updated'
                )
                ->orderBy('album.position')
                ->orderBy('puzzle.position')
                ->orderBy('p.position')
                ->get()
                ->map(function ($piece) {
                    return [
                        'piece_id' => $piece->piece_id,
                        'position' => $piece->position,
                        'stars' => $piece->stars,
                        'album_id' => $piece->album_id,
                        'album_name' => $piece->album_name,
                        'puzzle_id' => $piece->puzzle_id,
                        'puzzle_name' => $piece->puzzle_name,
                        'offers' => $piece->offers,
                        'last_updated' => $piece->last_updated,
                        'user' => [
                            'id' => $piece->user_id,
                            'username' => $piece->username,
                            'display_name' => $piece->display_name ?? $piece->username,
                        ],
                    ];
                })
                ->toArray();
        }

        // Find pieces that other users need that I'm offering
        $canHelpWith = [];
        if ($myOffers->isNotEmpty()) {
            $canHelpWith = DB::table('puzzles_user_puzzle_pieces as up')
                ->join('users as u', 'up.user_id', '=', 'u.id')
                ->join('puzzles_album_puzzle_pieces as p', 'up.puzzles_album_puzzle_piece_id', '=', 'p.id')
                ->join('puzzles_album_puzzles as puzzle', 'p.puzzles_album_puzzle_id', '=', 'puzzle.id')
                ->join('puzzles_albums as album', 'p.puzzles_album_id', '=', 'album.id')
                ->whereIn('up.puzzles_album_puzzle_piece_id', $myOffers)
                ->where('up.needs', true)
                ->where('up.user_id', '!=', $userId)
                ->select(
                    'p.id as piece_id',
                    'p.position',
                    'p.stars',
                    'puzzle.id as puzzle_id',
                    "puzzle.name->$locale as puzzle_name",
                    'album.id as album_id',
                    "album.name->$locale as album_name",
                    'u.id as user_id',
                    'u.username',
                    'u.display_name',
                    'up.updated_at as last_updated'
                )
                ->orderBy('album.position')
                ->orderBy('puzzle.position')
                ->orderBy('p.position')
                ->get()
                ->map(function ($piece) {
                    return [
                        'piece_id' => $piece->piece_id,
                        'position' => $piece->position,
                        'stars' => $piece->stars,
                        'album_id' => $piece->album_id,
                        'album_name' => $piece->album_name,
                        'puzzle_id' => $piece->puzzle_id,
                        'puzzle_name' => $piece->puzzle_name,
                        'user' => [
                            'id' => $piece->user_id,
                            'username' => $piece->username,
                            'display_name' => $piece->display_name ?? $piece->username,
                        ],
                        'last_updated' => $piece->last_updated,
                    ];
                })
                ->toArray();
        }

        return response()->json([
            'data' => [
                'can_get_from' => $canGetFrom,
                'can_help_with' => $canHelpWith,
            ],
            'meta' => [
                'my_needs_count' => $myNeeds->count(),
                'my_offers_count' => $myOffers->count(),
                'can_get_pieces_count' => count($canGetFrom),
                'can_help_pieces_count' => count($canHelpWith),
                'cached_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
