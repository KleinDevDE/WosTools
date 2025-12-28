<?php

namespace App\Models;

use \Spatie\Permission\Models\Role as SpatieRole;

/**
 * @mixin SpatieRole
 * @property int $weight
 */
class Role extends SpatieRole
{
    public function getFillable(): array
    {
        return array_merge(['weight'], parent::getFillable());
    }
}
