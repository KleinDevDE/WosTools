<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class CharacterInvitation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'player_id',
        'alliance_id',
        'invited_by_character_id',
        'token',
        'status',
        'accepted_at',
        'declined_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'player_id' => 'integer',
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(Character::class, 'invited_by_character_id');
    }

    public function invitationUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => URL::signedRoute('invitation.accept', ['token' => $this->token])
        );
    }
}
