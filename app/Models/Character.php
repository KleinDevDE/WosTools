<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Character extends Model
{
    use HasRolesAndAbilities, Notifiable;
    private static Character $activeCharacter;

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

    /**
     * Get the password for authentication (delegate to user)
     */
    public function getAuthPassword()
    {
        return $this->user->password;
    }

    /**
     * Get the name for display
     */
    public function getName(): string
    {
        return $this->player_name;
    }

    /**
     * @param Character $activeCharacter
     */
    public static function setActiveCharacter(Character $activeCharacter): void
    {
        self::$activeCharacter = $activeCharacter;
    }

    /**
     * @return Character|null
     */
    public static function getActiveCharacter(): ?Character
    {
        return self::$activeCharacter ?? null;
    }
}
