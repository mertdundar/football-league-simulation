<?php

namespace App\Models;

use Database\Factories\FixtureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['season_id', 'week', 'home_team_id', 'away_team_id', 'home_score', 'away_score', 'played_at'])]
class Fixture extends Model
{
    /** @use HasFactory<FixtureFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'week' => 'integer',
            'home_score' => 'integer',
            'away_score' => 'integer',
            'played_at' => 'datetime',
        ];
    }

    public function isPlayed(): bool
    {
        return $this->home_score !== null && $this->away_score !== null;
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
