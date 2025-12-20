<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuzzlesAlbumPuzzlePiece extends Model
{
    protected $fillable = [
        'puzzles_album_id', 'puzzles_album_puzzle_id', 'position', 'stars'
    ];

    public function puzzle(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbumPuzzle::class, 'puzzles_album_puzzle_id');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbum::class, 'puzzles_album_id');
    }
}
