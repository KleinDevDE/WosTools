<?php

namespace Modules\Puzzles\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PuzzlesAlbumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Puzzles\Models\PuzzlesAlbum::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

