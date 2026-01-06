<?php

namespace App\Models;

use Database\Factories\AllianceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alliance extends Model
{
    /** @use HasFactory<AllianceFactory> */
    use HasFactory;
    protected $fillable = [
        'state_id', 'name', 'tag'
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
