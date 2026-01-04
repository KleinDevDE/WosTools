<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Permissions\PermissionsSeeder;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (User::count() === 0) {
            $user = User::factory()->create([
                'player_id' => 1,
                'player_name' => 'test',
                'email_verified_at' => now(),
                'password' => Hash::make(hash('sha256', 'test')),
                'status' => User::STATUS_ACTIVE
            ]);
            $user->assignRole(['user', 'management', 'developer']);

            User::factory(29)->afterCreating(function (User $user) {
                $user->assignRole('user');
            })->create();
        }
    }
}
