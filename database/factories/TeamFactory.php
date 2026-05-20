<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'short_name' => strtoupper(fake()->unique()->lexify('???')),
            'strength' => fake()->numberBetween(1, 96),
            'advantage_home' => 3,
        ];
    }
}
