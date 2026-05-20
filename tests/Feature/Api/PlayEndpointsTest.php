<?php

namespace Tests\Feature\Api;

use App\Models\Fixture;
use App\Models\Season;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TeamSeeder::class);
    }

    public function test_play_next_without_a_season_returns_409(): void
    {
        $this->postJson('/api/play-next')
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'NO_FIXTURES');
    }

    public function test_play_next_plays_the_first_week_after_generate(): void
    {
        $this->postJson('/api/fixtures/generate')->assertStatus(201);

        $this->postJson('/api/play-next')
            ->assertOk()
            ->assertJsonPath('status', 'in_progress')
            ->assertJsonPath('current_week', 1);

        $this->assertSame(2, Fixture::whereNotNull('played_at')->count());
    }

    public function test_play_next_advances_one_week_at_a_time(): void
    {
        $this->postJson('/api/fixtures/generate');

        for ($w = 1; $w <= 6; $w++) {
            $this->postJson('/api/play-next')
                ->assertOk()
                ->assertJsonPath('current_week', $w);
        }

        $this->getJson('/api/state')
            ->assertJsonPath('status', 'complete')
            ->assertJsonPath('current_week', 6);
    }

    public function test_play_next_on_complete_season_returns_409(): void
    {
        $this->postJson('/api/fixtures/generate');
        $this->postJson('/api/play-all');

        $this->postJson('/api/play-next')
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'SEASON_COMPLETE');
    }

    public function test_play_all_plays_every_remaining_week(): void
    {
        $this->postJson('/api/fixtures/generate');

        $this->postJson('/api/play-all')
            ->assertOk()
            ->assertJsonPath('status', 'complete')
            ->assertJsonPath('current_week', 6);

        $this->assertSame(12, Fixture::whereNotNull('played_at')->count());
        $this->assertSame(0, Fixture::whereNull('home_score')->count());
    }

    public function test_play_all_is_idempotent_when_complete(): void
    {
        $this->postJson('/api/fixtures/generate');
        $this->postJson('/api/play-all');

        $this->postJson('/api/play-all')
            ->assertOk()
            ->assertJsonPath('status', 'complete');
    }

    public function test_play_all_without_a_season_returns_409(): void
    {
        $this->postJson('/api/play-all')
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'NO_FIXTURES');
    }

    public function test_play_all_continues_from_mid_season(): void
    {
        $this->postJson('/api/fixtures/generate');
        $this->postJson('/api/play-next'); // week 1
        $this->postJson('/api/play-next'); // week 2

        $this->postJson('/api/play-all')
            ->assertOk()
            ->assertJsonPath('status', 'complete')
            ->assertJsonPath('current_week', 6);

        $this->assertSame(12, Fixture::whereNotNull('played_at')->count());
    }

    public function test_predictions_show_up_from_week_four(): void
    {
        $this->postJson('/api/fixtures/generate');
        for ($w = 1; $w <= 3; $w++) {
            $this->postJson('/api/play-next');
        }

        $afterThree = $this->getJson('/api/state')->json('predictions');
        $this->assertNull($afterThree, 'Predictions should still be null after week 3.');

        $this->postJson('/api/play-next');

        $afterFour = $this->getJson('/api/state')->json('predictions');
        $this->assertIsArray($afterFour);
        $this->assertCount(4, $afterFour);
    }
}
