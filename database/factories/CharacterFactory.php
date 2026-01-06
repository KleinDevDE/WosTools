<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::getQuery()->inRandomOrder()->first()->id,
            'player_id' => $this->faker->unique()->numberBetween(10,10000),
            'name' => $this->faker->name(),
        ];
    }
}
