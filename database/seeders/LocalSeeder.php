<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\State;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Bouncer;

class LocalSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (User::count() === 0) {
            $state = State::create(['id' => 1]);
            $alliance = $state->alliances()->create(['alliance_name' => 'Test Alliance']);

            $user = User::factory()->create([
                'username' => 'test',
                'email_verified_at' => now(),
                'password' => Hash::make(hash('sha256', 'test')),
            ]);

            /**
             * @var Character $char
             */
            $char = $user->characters()->create(['state' => 1, 'alliance_id' => $alliance->id, 'player_id' => 1, 'player_name' => 'test']);
            app(Bouncer::class)->assign('developer')->to($user);
            app(Bouncer::class)->assign('developer')->to($char);
        }
    }
}
