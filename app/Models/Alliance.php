<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alliance extends Model
{
    protected $fillable = [
        'state',
        'alliance_name',
        'alliance_tag',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(CharacterAllianceHistory::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CharacterInvitation::class);
    }
}
