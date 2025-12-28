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
                    'pages:dashboard:view',
                    'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                ]
            ],
            'test2' => [
                'weight' => 10,
                'inherits' => [],
                'permissions' => [
                    'pages:dashboard:view',
                    'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                ]
            ],
            'management' => [
                'weight' => 80,
                'inherits' => ['user'],
                'permissions' => [
                    'puzzles::albums.view', 'puzzles::puzzles.view', 'puzzles::pieces.view',
                    'users.show', 'users.invite', 'users.lock'
                ]
            ],
            'developer' => [
                'weight' => 100,
                'inherits' => ['management'],
                'permissions' => [
                    'puzzles::albums.view', 'puzzles::albums.create', 'puzzles::albums.edit', 'puzzles::albums.delete',
                    'puzzles::puzzles.view', 'puzzles::puzzles.create', 'puzzles::puzzles.edit', 'puzzles::puzzles.delete',
                    'puzzles::pieces.view', 'puzzles::pieces.create', 'puzzles::pieces.edit', 'puzzles::pieces.delete',
                    'users.show', 'users.edit', 'users.delete', 'users.lock', 'users.invite',
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
