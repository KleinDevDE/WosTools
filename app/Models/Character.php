<?php

namespace App\Models;

use App\Traits\HasRoleHierarchy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Permission\Traits\HasRoles;

class Character extends Model
{
    use HasRoles, HasRoleHierarchy;

    protected $fillable = [
        'user_id',
        'player_id',
        'player_name',
        'state',
        'alliance_id',
    ];

    protected function casts(): array
    {
        return [
            'player_id' => 'integer',
            'state' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stateRelation(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    public function allianceHistory(): HasMany
    {
        return $this->hasMany(CharacterAllianceHistory::class);
    }

    public function characterStats(): HasMany
    {
        return $this->hasMany(CharacterStats::class, 'player_id', 'player_id');
    }

    public function latestStats(): HasMany
    {
        return $this->characterStats()->latestOfMany();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CharacterInvitation::class, 'invited_by_character_id');
    }
}
