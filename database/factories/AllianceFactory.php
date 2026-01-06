<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alliance>
 */
class AllianceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'state_id' => State::query()->inRandomOrder()->first()->id,
            'name' => $this->faker->company(),
            'tag' => strtoupper($this->faker->lexify('???')),
        ];
    }
}
