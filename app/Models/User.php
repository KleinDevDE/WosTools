<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'last_login_at',
        'locale'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function activeCharacter(): ?Character
    {
        $characterId = session('active_character_id');

        if (!$characterId) {
            return null;
        }

        return $this->characters()->find($characterId);
    }

    // Proxy methods for Spatie Permission (delegates to active character)
    public function hasRole($roles, string $guard = null): bool
    {
        return $this->activeCharacter()?->hasRole($roles, $guard) ?? false;
    }

    public function hasAnyRole($roles, string $guard = null): bool
    {
        return $this->activeCharacter()?->hasAnyRole($roles, $guard) ?? false;
    }

    public function hasAllRoles($roles, string $guard = null): bool
    {
        return $this->activeCharacter()?->hasAllRoles($roles, $guard) ?? false;
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->activeCharacter()?->hasPermissionTo($permission, $guardName) ?? false;
    }

    public function hasAnyPermission(...$permissions): bool
    {
        return $this->activeCharacter()?->hasAnyPermission(...$permissions) ?? false;
    }

    public function hasAllPermissions(...$permissions): bool
    {
        return $this->activeCharacter()?->hasAllPermissions(...$permissions) ?? false;
    }

    public function getName(): string
    {
        return $this->activeCharacter()?->player_name ?? $this->username;
    }
}
