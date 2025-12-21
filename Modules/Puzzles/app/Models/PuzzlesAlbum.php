<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PuzzlesAlbum extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function puzzles(): HasMany
    {
        return $this->hasMany(PuzzlesAlbumPuzzle::class, 'puzzles_album_id')->orderBy('position');
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(PuzzlesAlbumPuzzlePiece::class, 'puzzles_album_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }
}
