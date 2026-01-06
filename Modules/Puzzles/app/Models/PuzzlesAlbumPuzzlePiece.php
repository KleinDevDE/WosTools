<?php

namespace Modules\Puzzles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PuzzlesAlbumPuzzlePiece extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'puzzles_album_id',
        'puzzles_album_puzzle_id',
        'position',
        'stars',
    ];

    protected $casts = [
        'position' => 'integer',
        'stars' => 'integer',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbum::class, 'puzzles_album_id');
    }

    public function puzzle(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbumPuzzle::class, 'puzzles_album_puzzle_id');
    }

    public function characterStates(): HasMany
    {
        return $this->hasMany(PuzzlesUserPuzzlePiece::class, 'puzzles_album_puzzle_piece_id');
    }

    public function isTradeable(): bool
    {
        return $this->stars < 5;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(400)
            ->height(400)
            ->sharpen(10);
    }
}
