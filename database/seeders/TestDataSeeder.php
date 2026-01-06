<?php

namespace Database\Seeders;

use App\Models\Alliance;
use App\Models\Character;
use App\Models\CharacterInvitation;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Seed test data for multi-character system
     */
    public function run(): void
    {
        // Create test states
        $state1234 = State::firstOrCreate(['id' => 1234]);
        $state5678 = State::firstOrCreate(['id' => 5678]);

        // Create test alliances
        $alliance1 = Alliance::firstOrCreate([
            'state' => 1234,
            'alliance_name' => 'Test Alliance Alpha',
        ], [
            'alliance_tag' => 'TAA',
        ]);

        $alliance2 = Alliance::firstOrCreate([
            'state' => 1234,
            'alliance_name' => 'Test Alliance Beta',
        ], [
            'alliance_tag' => 'TAB',
        ]);

        $alliance3 = Alliance::firstOrCreate([
            'state' => 5678,
            'alliance_name' => 'Test Alliance Gamma',
        ], [
            'alliance_tag' => 'TAG',
        ]);

        // Create test users with characters

        // Developer user with multiple characters
        $developer = User::firstOrCreate(
            ['username' => 'developer'],
            [
                'email' => 'developer@test.com',
                'password' => Hash::make(hash('sha256', 'password123')),
                'locale' => 'en',
            ]
        );

        $devChar1 = Character::firstOrCreate([
            'player_id' => 1000001,
        ], [
            'user_id' => $developer->id,
            'player_name' => 'DevMaster',
            'state' => 1234,
            'alliance_id' => $alliance1->id,
        ]);
        $devChar1->assignRole('developer');

        $devChar2 = Character::firstOrCreate([
            'player_id' => 1000002,
        ], [
            'user_id' => $developer->id,
            'player_name' => 'DevAlt',
            'state' => 5678,
            'alliance_id' => $alliance3->id,
        ]);
        $devChar2->assignRole('developer');

        // R5 Alliance Leader
        $r5User = User::firstOrCreate(
            ['username' => 'alliance_leader'],
            [
                'email' => 'r5@test.com',
                'password' => Hash::make(hash('sha256', 'password123')),
                'locale' => 'en',
            ]
        );

        $r5Char = Character::firstOrCreate([
            'player_id' => 2000001,
        ], [
            'user_id' => $r5User->id,
            'player_name' => 'AllianceLeader',
            'state' => 1234,
            'alliance_id' => $alliance1->id,
        ]);
        $r5Char->assignRole('wos_r5');

        // R4 Alliance Manager
        $r4User = User::firstOrCreate(
            ['username' => 'alliance_manager'],
            [
                'email' => 'r4@test.com',
                'password' => Hash::make(hash('sha256', 'password123')),
                'locale' => 'en',
            ]
        );

        $r4Char = Character::firstOrCreate([
            'player_id' => 3000001,
        ], [
            'user_id' => $r4User->id,
            'player_name' => 'AllianceManager',
            'state' => 1234,
            'alliance_id' => $alliance1->id,
        ]);
        $r4Char->assignRole('wos_r4');

        // Regular users with single character
        $user1 = User::firstOrCreate(
            ['username' => 'testuser1'],
            [
                'email' => 'user1@test.com',
                'password' => Hash::make(hash('sha256', 'password123')),
                'locale' => 'de',
            ]
        );

        $char1 = Character::firstOrCreate([
            'player_id' => 4000001,
        ], [
            'user_id' => $user1->id,
            'player_name' => 'TestPlayer1',
            'state' => 1234,
            'alliance_id' => $alliance1->id,
        ]);
        $char1->assignRole('user');

        $user2 = User::firstOrCreate(
            ['username' => 'testuser2'],
            [
                'email' => 'user2@test.com',
                'password' => Hash::make(hash('sha256', 'password123')),
                'locale' => 'tr',
            ]
        );

        $char2 = Character::firstOrCreate([
            'player_id' => 5000001,
        ], [
            'user_id' => $user2->id,
            'player_name' => 'TestPlayer2',
            'state' => 1234,
            'alliance_id' => $alliance2->id,
        ]);
        $char2->assignRole('user');

        // User without alliance
        $user3 = User::firstOrCreate(
            ['username' => 'testuser3'],
            [
                'email' => 'user3@test.com',
                'password' => Hash::make(hash('sha256', 'password123')),
                'locale' => 'en',
            ]
        );

        $char3 = Character::firstOrCreate([
            'player_id' => 6000001,
        ], [
            'user_id' => $user3->id,
            'player_name' => 'SoloPlayer',
            'state' => 1234,
            'alliance_id' => null,
        ]);
        $char3->assignRole('user');

        // Create some test invitations
        CharacterInvitation::firstOrCreate([
            'player_id' => 7000001,
            'alliance_id' => $alliance1->id,
        ], [
            'invited_by_character_id' => $r5Char->id,
            'token' => Str::random(64),
            'status' => CharacterInvitation::STATUS_PENDING,
        ]);

        CharacterInvitation::firstOrCreate([
            'player_id' => 7000002,
            'alliance_id' => $alliance1->id,
        ], [
            'invited_by_character_id' => $r4Char->id,
            'token' => Str::random(64),
            'status' => CharacterInvitation::STATUS_PENDING,
        ]);

        $this->command->info('Test data seeded successfully!');
        $this->command->info('');
        $this->command->info('Test Accounts:');
        $this->command->info('  Developer: username=developer, password=password123');
        $this->command->info('  R5 Leader: username=alliance_leader, password=password123');
        $this->command->info('  R4 Manager: username=alliance_manager, password=password123');
        $this->command->info('  User 1: username=testuser1, password=password123');
        $this->command->info('  User 2: username=testuser2, password=password123');
        $this->command->info('  User 3: username=testuser3, password=password123');
    }
}
