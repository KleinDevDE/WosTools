<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuzzlesAlbumPuzzle extends Model
{
    protected $fillable = [
        'puzzles_album_id', 'name', 'position'
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbum::class, 'puzzles_album_id');
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(PuzzlesAlbumPuzzlePiece::class, 'puzzles_album_puzzle_id');
    }
}
