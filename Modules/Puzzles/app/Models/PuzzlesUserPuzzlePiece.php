<?php

namespace Modules\Puzzles\Models;

use App\Models\Character;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuzzlesUserPuzzlePiece extends Model
{
    protected $table = 'puzzles_character_puzzle_pieces';

    public $incrementing = false;

    protected $primaryKey = ['character_id', 'puzzles_album_puzzle_piece_id'];

    protected $fillable = [
        'character_id',
        'puzzles_album_puzzle_piece_id',
        'needs',
        'owns',
        'offers',
    ];

    protected $casts = [
        'needs' => 'boolean',
        'owns' => 'boolean',
        'offers' => 'integer',
    ];

    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function piece(): BelongsTo
    {
        return $this->belongsTo(PuzzlesAlbumPuzzlePiece::class, 'puzzles_album_puzzle_piece_id');
    }

    public function needsPiece(): bool
    {
        return $this->needs;
    }

    public function ownsForCollection(): bool
    {
        return $this->owns;
    }

    public function isOffering(): bool
    {
        return $this->offers > 0;
    }

    public function isTradeable(): bool
    {
        return $this->piece && $this->piece->stars < 5;
    }
}
