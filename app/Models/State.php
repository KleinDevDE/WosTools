<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;

    protected $keyType = 'int';

    public function alliances(): HasMany
    {
        return $this->hasMany(Alliance::class, 'state', 'id');
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'state', 'id');
    }
}
