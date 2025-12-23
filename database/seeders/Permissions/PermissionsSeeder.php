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
            'restricted' => [
                'pages:dashboard:view',
            ],
            'user' => [
                'pages:dashboard:view',
                'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                'puzzles::view',
            ],
            'management' => [
                'pages:dashboard:view',
                'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                'puzzles::albums.view', 'puzzles::puzzles.view', 'puzzles::pieces.view',
                'users.show', 'users.invite'
            ],
            'developer' => [
                'pages:dashboard:view',
                'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                'puzzles::albums.view', 'puzzles::albums.create', 'puzzles::albums.edit', 'puzzles::albums.delete',
                'puzzles::puzzles.view', 'puzzles::puzzles.create', 'puzzles::puzzles.edit', 'puzzles::puzzles.delete',
                'puzzles::pieces.view', 'puzzles::pieces.create', 'puzzles::pieces.edit', 'puzzles::pieces.delete',
                'users.show', 'users.edit', 'users.delete', 'users.invite'
            ]
        ];

        $allPermissions = [];
        foreach ($permissions as $roleName =>  $group) {
            foreach ($group AS $index => $permission) {
                $permissions[$roleName][$index] = $allPermissions[$permission] ?? Permission::updateOrCreate(['name' => $permission]);
                $allPermissions[$permission] = $permissions[$roleName][$index];
            }
        }


        foreach ($permissions as $roleName => $group) {
            $role = Role::updateOrCreate(['name' => $roleName]);

            $role->syncPermissions(...$group);
        }
    }
}
