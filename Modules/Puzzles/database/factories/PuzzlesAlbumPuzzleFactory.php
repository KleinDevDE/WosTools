<?php

namespace Modules\Puzzles\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PuzzlesAlbumPuzzleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Puzzles\Models\PuzzlesAlbumPuzzle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

