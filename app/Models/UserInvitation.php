<?php

namespace App\Models;

use App\Services\UserInvitationService;
use Database\Factories\UserInvitationFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * @property string $invitationURL
 */
class UserInvitation extends Model
{
    /** @use HasFactory<UserInvitationFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'username', 'invited_by', 'revoked_by',
        'token', 'status', 'accepted_at', 'revoked_at'
    ];

    protected $hidden = [
        'token'
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function revoke(): void
    {
        UserInvitationService::revokeInvitation($this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function invitationURLAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => URL::signedRoute('auth.register', ['token' => $this->token])
        );
    }
}
