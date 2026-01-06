<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAllianceHistory extends Model
{
    public const REASON_KICKED = 'kicked';
    public const REASON_SELF = 'self';
    public const REASON_R5_TRANSFER = 'r5_transfer';

    protected $table = 'character_alliance_history';

    protected $fillable = [
        'character_id',
        'alliance_id',
        'joined_at',
        'left_at',
        'left_reason',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }
}
