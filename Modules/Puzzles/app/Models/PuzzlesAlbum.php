<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuzzlesAlbum extends Model
{
    protected $fillable = [
        'name'
    ];

    public function puzzles(): HasMany
    {
        return $this->hasMany(PuzzlesAlbumPuzzle::class, 'puzzles_album_id');
    }
}
