<?php

namespace Database\Seeders\Permissions;

use Bouncer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class PermissionsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Bouncer::allow('superadmin')->everything();


        $roles = [
            'user' => [
                'title' => 'User',
                'inherits' => [],
                'abilities' => [
                    'dashboard.view',
                    'puzzles::view', 'puzzles::own.view', 'puzzles::own.manage',
                    'profile.view', 'profile.edit',
                ]
            ],
            'wos_r4' => [
                'title' => 'WoS R4',
                'inherits' => ['user'],
                'abilities' => [
                    'alliance.view',
                    'alliance.members.list',
                    'alliance.members.invite',
                    'alliance.members.kick',
                ]
            ],
            'wos_r5' => [
                'title' => 'WoS R5',
                'inherits' => ['wos_r4'],
                'abilities' => [
                    'alliance.edit',
                    'alliance.members.promote.r4',
                    'alliance.members.demote.r4',
                    'alliance.role.transfer.r5',
                ]
            ],
            'developer' => [
                'title' => 'Developer',
                'inherits' => [],
                'abilities' => [
                    'puzzles::albums.view', 'puzzles::albums.create', 'puzzles::albums.edit', 'puzzles::albums.delete',
                    'puzzles::puzzles.view', 'puzzles::puzzles.create', 'puzzles::puzzles.edit', 'puzzles::puzzles.delete',
                    'puzzles::pieces.view', 'puzzles::pieces.create', 'puzzles::pieces.edit', 'puzzles::pieces.delete',
                    'users.show', 'users.edit', 'users.delete', 'users.invite',
                    'characters.view', 'characters.edit', 'characters.delete',
                    'alliances.create', 'alliances.edit', 'alliances.delete',
                    'media.gallery.view', 'media.gallery.edit', 'media.gallery.delete'
                ]
            ]
        ];

        // Collect all unique abilities
        $allAbilities = [];
        foreach ($roles as $roleName => $roleData) {
            foreach ($roleData['abilities'] as $abilityName) {
                if (!isset($allAbilities[$abilityName])) {
                    $allAbilities[$abilityName] = true;
                }
            }
        }

        // Create all abilities
        foreach (array_keys($allAbilities) as $abilityName) {
            Ability::firstOrCreate(['name' => $abilityName]);
        }

        // Create roles and assign abilities
        foreach ($roles as $roleName => $roleData) {
            $role = Bouncer::role()->firstOrCreate(
                ['name' => $roleName],
                ['title' => $roleData['title']]
            );

            // Collect all abilities for this role (including inherited)
            $roleAbilities = $roleData['abilities'];
            foreach ($roleData['inherits'] as $inheritedRoleName) {
                if (isset($roles[$inheritedRoleName])) {
                    $roleAbilities = array_merge($roles[$inheritedRoleName]['abilities'], $roleAbilities);
                }
            }

            // Remove duplicate abilities
            $roleAbilities = array_unique($roleAbilities);

            // Sync abilities to role
            Bouncer::sync($role)->abilities($roleAbilities);
        }

        // Clean up roles that are not in our list
        Role::whereNotIn('name', array_keys($roles))->delete();

        // Clean up abilities that are no longer used
        $usedAbilities = array_keys($allAbilities);
        Ability::whereNotIn('name', $usedAbilities)->delete();
    }
}
