<?php

namespace Tests\Unit\Services;

use App\Services\MatchSimulator;
use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Random\Randomizer;

class MatchSimulatorTest extends TestCase
{
    private function simulatorWithSeed(int $seed): MatchSimulator
    {
        return new MatchSimulator(new Randomizer(new Mt19937($seed)));
    }

    public function test_same_seed_produces_identical_scores(): void
    {
        $a = $this->simulatorWithSeed(42)->simulate(80, 70);
        $b = $this->simulatorWithSeed(42)->simulate(80, 70);
        $this->assertSame($a, $b);
    }

    public function test_different_seeds_can_produce_different_scores(): void
    {
        $a = $this->simulatorWithSeed(1)->simulate(80, 80);
        $b = $this->simulatorWithSeed(2)->simulate(80, 80);
        // Not a guaranteed difference for a single run, but extremely likely.
        $allSame = true;
        $sim1 = $this->simulatorWithSeed(1);
        $sim2 = $this->simulatorWithSeed(2);
        for ($i = 0; $i < 5; $i++) {
            if ($sim1->simulate(80, 80) !== $sim2->simulate(80, 80)) {
                $allSame = false;
                break;
            }
        }
        $this->assertFalse($allSame, 'Two different seeds always matched — suspicious.');
    }

    public function test_scores_are_non_negative_integers(): void
    {
        $sim = $this->simulatorWithSeed(7);
        for ($i = 0; $i < 100; $i++) {
            $r = $sim->simulate(80, 80);
            $this->assertIsInt($r['home']);
            $this->assertIsInt($r['away']);
            $this->assertGreaterThanOrEqual(0, $r['home']);
            $this->assertGreaterThanOrEqual(0, $r['away']);
        }
    }

    public function test_stronger_side_wins_more_often_than_loses(): void
    {
        $sim = $this->simulatorWithSeed(1);
        $wins = $losses = 0;
        for ($i = 0; $i < 1000; $i++) {
            $r = $sim->simulate(92, 60, 0);
            if ($r['home'] > $r['away']) {
                $wins++;
            } elseif ($r['home'] < $r['away']) {
                $losses++;
            }
        }
        $this->assertGreaterThan($losses * 3, $wins, "Strong home (92) lost too often to weak away (60): wins={$wins}, losses={$losses}");
    }

    public function test_advantage_home_increases_home_wins_on_balanced_teams(): void
    {
        $simNo = $this->simulatorWithSeed(1);
        $simYes = $this->simulatorWithSeed(1);
        $winsNo = $winsYes = 0;

        for ($i = 0; $i < 500; $i++) {
            $a = $simNo->simulate(80, 80, 0);
            $b = $simYes->simulate(80, 80, 12);
            if ($a['home'] > $a['away']) {
                $winsNo++;
            }
            if ($b['home'] > $b['away']) {
                $winsYes++;
            }
        }

        $this->assertGreaterThan($winsNo, $winsYes, "Home advantage of 12 didn't tilt outcomes: winsNo={$winsNo}, winsYes={$winsYes}");
    }

    public function test_upset_is_possible_but_uncommon(): void
    {
        $sim = $this->simulatorWithSeed(3);
        $upsets = 0;
        for ($i = 0; $i < 1000; $i++) {
            $r = $sim->simulate(95, 50, 0);
            if ($r['away'] > $r['home']) {
                $upsets++;
            }
        }
        $this->assertGreaterThan(0, $upsets, 'Upsets must be possible.');
        $this->assertLessThan(150, $upsets, "Upsets should be rare (<15%), got {$upsets}/1000.");
    }
}
