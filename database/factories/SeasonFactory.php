<?php

namespace Database\Factories;

use App\Enums\SeasonStatus;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Season>
 */
class SeasonFactory extends Factory
{
    protected $model = Season::class;

    public function definition(): array
    {
        return [
            'status' => SeasonStatus::Setup,
            'current_week' => null,
            'total_weeks' => 6, //Based-on given 4 teams in the league expectation
        ];
    }

    public function inProgress(int $currentWeek = 1): static
    {
        return $this->state([
            'status' => SeasonStatus::InProgress,
            'current_week' => $currentWeek,
        ]);
    }

    public function complete(): static
    {
        return $this->state([
            'status' => SeasonStatus::Complete,
            'current_week' => 6, //Based-on given 4 teams in the league expectation
        ]);
    }
}
