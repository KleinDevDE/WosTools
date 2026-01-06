<?php

namespace App\Models;

use Database\Factories\CharacterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Character extends Model
{
    /** @use HasFactory<CharacterFactory> */
    use HasFactory;

    protected $fillable = [
        'state_id', 'alliance_id', 'user_id', 'player_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
