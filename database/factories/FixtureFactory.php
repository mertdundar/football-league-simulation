<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fixture>
 */
class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'week' => 1,
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'home_score' => null,
            'away_score' => null,
            'played_at' => null,
        ];
    }

    public function played(int $homeScore, int $awayScore): static
    {
        return $this->state([
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'played_at' => now(),
        ]);
    }
}
