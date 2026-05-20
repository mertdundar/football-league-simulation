<?php

namespace App\Models;

use App\Enums\SeasonStatus;
use Database\Factories\SeasonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['status', 'current_week', 'total_weeks'])]
class Season extends Model
{
    /** @use HasFactory<SeasonFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => SeasonStatus::class,
            'current_week' => 'integer',
            'total_weeks' => 'integer',
        ];
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }
}
