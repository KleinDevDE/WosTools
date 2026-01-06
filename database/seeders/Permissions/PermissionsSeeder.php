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
        $permissions = [
            'user' => [
                'weight' => 0,
                'inherits' => [],
                'permissions' => [
                    'dashboard.view',
                    'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                    'profile.view', 'profile.edit',
                ]
            ],
            'wos_r4' => [
                'weight' => 40,
                'inherits' => ['user'],
                'permissions' => [
                    'alliance.view',
                    'alliance.members.list',
                    'alliance.members.invite',
                    'alliance.members.kick',
                    'alliance.members.lock',
                    'alliance.members.unlock',
                ]
            ],
            'wos_r5' => [
                'weight' => 50,
                'inherits' => ['wos_r4'],
                'permissions' => [
                    'alliance.edit',
                    'alliance.members.promote.r4',
                    'alliance.members.demote.r4',
                    'alliance.role.transfer.r5',
                ]
            ],
            'developer' => [
                'weight' => 100,
                'inherits' => [],
                'permissions' => [
                    'puzzles::albums.view', 'puzzles::albums.create', 'puzzles::albums.edit', 'puzzles::albums.delete',
                    'puzzles::puzzles.view', 'puzzles::puzzles.create', 'puzzles::puzzles.edit', 'puzzles::puzzles.delete',
                    'puzzles::pieces.view', 'puzzles::pieces.create', 'puzzles::pieces.edit', 'puzzles::pieces.delete',
                    'users.show', 'users.edit', 'users.delete', 'users.lock', 'users.invite',
                    'characters.view', 'characters.edit', 'characters.delete',
                    'alliances.create', 'alliances.edit', 'alliances.delete',
                    'media.gallery.view', 'media.gallery.edit', 'media.gallery.delete'
                ]
            ]
        ];

        $allPermissions = [];
        $knownPermissions = Permission::pluck('name')->toArray();

        foreach ($permissions as $roleName => $group) {
            foreach ($group['permissions'] AS $index => $permission) {
                $permissions[$roleName]['permissions'][$index] = $allPermissions[$permission] ?? Permission::updateOrCreate(['name' => $permission]);
                $allPermissions[$permission] = $permissions[$roleName]['permissions'][$index];
            }
        }

        //Handle inherited permissions
        foreach ($permissions as $roleName => $group) {
            foreach ($group['inherits'] AS $inheritedRole) {
                $permissions[$roleName]['permissions'] = array_merge($permissions[$inheritedRole]['permissions'], $permissions[$roleName]['permissions']);
            }
        }

        $permsToDelete = array_diff($knownPermissions, array_keys($allPermissions));
        Permission::whereIn('name', $permsToDelete)->delete();

        foreach ($permissions as $roleName => $group) {
            $role = Role::updateOrCreate(['name' => $roleName], ['weight' => $group['weight']]);

            $role->syncPermissions(...$group['permissions']);
        }

        Role::whereNotIn('name', array_keys($permissions))->delete();
    }
}
