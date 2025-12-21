<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PuzzlesUserPuzzlePiece extends Model
{
    protected $table = 'puzzles_user_puzzle_pieces';

    protected $fillable = [
        'user_id',
        'puzzles_album_puzzle_piece_id',
        'state',
    ];

    protected $casts = [
        'state' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function piece(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbumPuzzlePiece::class, 'puzzles_album_puzzle_piece_id');
    }

    public function isNeed(): bool
    {
        return $this->state === 'need';
    }

    public function isHave(): bool
    {
        return $this->state === 'have';
    }

    public function isTradeable(): bool
    {
        return $this->piece && $this->piece->stars < 5;
    }
}
