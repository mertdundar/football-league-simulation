<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    private const array TEAMS = [
        ['name' => 'Team 93',         'short_name' => 'T93', 'strength' => 93],
        ['name' => 'Team 88',         'short_name' => 'T88', 'strength' => 88],
        ['name' => 'Team 82',         'short_name' => 'T82', 'strength' => 82],
        ['name' => 'Team 75',         'short_name' => 'T75', 'strength' => 75],
    ];

    public function run(): void
    {
        foreach (self::TEAMS as $team) {
            Team::updateOrCreate(
                ['name' => $team['name']],
                $team + ['advantage_home' => 3],
            );
        }
    }
}
