<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PuzzlesAlbumPuzzle extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'puzzles_album_id',
        'name',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbum::class, 'puzzles_album_id');
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(PuzzlesAlbumPuzzlePiece::class, 'puzzles_album_puzzle_id')->orderBy('position');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
