<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\User;

/**
 * @mixin User
 */
trait HasRoleHierarchy
{
    public const SUPER_ADMIN_WEIGHT = 100;

    public function getHighestWeight(): int
    {
        return $this->roles->max('weight') ?? 0;
    }

    public function canManageRole(string|Role $role): bool
    {
        if (is_string($role)) {
            $role = Role::findByName($role);
        }

        $highestWeight = $this->getHighestWeight();
        return $highestWeight >= self::SUPER_ADMIN_WEIGHT || $highestWeight >= $role->weight;
    }

    public function canManageUser(User $user): bool
    {
        $highestWeight = $this->getHighestWeight();
        return $highestWeight >= self::SUPER_ADMIN_WEIGHT || $highestWeight >= $user->getHighestWeight();
    }

    public function cannotManage($user): bool
    {
        return !$this->canManage($user);
    }
}
