<?php

namespace Tests\Feature\Api;

use App\Models\Fixture;
use App\Models\Season;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TeamSeeder::class);
    }

    public function test_get_state_on_fresh_db_returns_setup_with_four_teams(): void
    {
        $response = $this->getJson('/api/state');

        $response->assertOk()
            ->assertJsonPath('status', 'setup')
            ->assertJsonPath('current_week', null)
            ->assertJsonPath('total_weeks', 6)
            ->assertJsonCount(4, 'teams')
            ->assertJsonPath('predictions', null)
            ->assertJsonPath('table', []);
    }

    public function test_generate_fixtures_returns_201_and_creates_twelve_unplayed_fixtures(): void
    {
        $response = $this->postJson('/api/fixtures/generate');

        $response->assertStatus(201)
            ->assertJsonPath('status', 'in_progress')
            ->assertJsonPath('current_week', null);

        $this->assertSame(1, Season::count());
        $this->assertSame(12, Fixture::count());
        $this->assertSame(0, Fixture::whereNotNull('home_score')->count());
    }

    public function test_generate_when_season_in_progress_returns_409(): void
    {
        $this->postJson('/api/fixtures/generate')->assertStatus(201);

        $response = $this->postJson('/api/fixtures/generate');

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'FIXTURES_EXIST');
    }

    public function test_reset_wipes_all_seasons_and_fixtures(): void
    {
        $this->postJson('/api/fixtures/generate')->assertStatus(201);

        $response = $this->postJson('/api/reset');

        $response->assertOk()->assertJsonPath('status', 'setup');
        $this->assertSame(0, Season::count());
        $this->assertSame(0, Fixture::count());
    }

    public function test_reset_on_fresh_db_is_safe_and_idempotent(): void
    {
        $first = $this->postJson('/api/reset');
        $second = $this->postJson('/api/reset');

        $first->assertOk()->assertJsonPath('status', 'setup');
        $second->assertOk()->assertJsonPath('status', 'setup');
    }

    public function test_regenerating_fixtures_can_produce_different_schedules(): void
    {
        $signatures = [];
        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/api/reset');
            $body = $this->postJson('/api/fixtures/generate')->json();
            $sig = collect($body['week_fixtures']['1'])
                ->map(fn (array $f): string => $f['home']['id'].'v'.$f['away']['id'])
                ->sort()
                ->implode(',');
            $signatures[$sig] = true;
        }

        $this->assertGreaterThan(
            1,
            count($signatures),
            'Expected at least two distinct week-1 matchup sets across 20 regenerations; got only '
                .count($signatures).'. The fixture generator may be deterministic.'
        );
    }
}
