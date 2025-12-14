<?php

namespace Database\Seeders\Permissions;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $userPermissions = [
            'view' => Permission::updateOrCreate(['name' => 'view users']),
            'create' => Permission::updateOrCreate(['name' => 'create users']),
            'edit' => Permission::updateOrCreate(['name' => 'edit users']),
            'delete' => Permission::updateOrCreate(['name' => 'delete users']),
            'restore' => Permission::updateOrCreate(['name' => 'restore users']),
        ];
        $userInvitationsPermissions = [
            'viewOwn' => Permission::updateOrCreate(['name' => 'view own userInvitation']),
            'view' => Permission::updateOrCreate(['name' => 'view userInvitations']),
            'create' => Permission::updateOrCreate(['name' => 'create userInvitations']),
            'edit' => Permission::updateOrCreate(['name' => 'edit userInvitations']),
            'delete' => Permission::updateOrCreate(['name' => 'delete userInvitations']),
        ];

        $developerRole = Role::updateOrCreate(['name' => 'developer']);
        $developerRole->syncPermissions($userPermissions + $userInvitationsPermissions);

        $managementRole = Role::updateOrCreate(['name' => 'management']);
        $managementRole->syncPermissions($userPermissions + $userInvitationsPermissions);

        $userRole = Role::updateOrCreate(['name' => 'user']);
        $userRole->syncPermissions([$userPermissions['view'], $userInvitationsPermissions['viewOwn']]);
    }
}
