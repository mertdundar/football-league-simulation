<?php

namespace App\Services;

use Random\Randomizer;

class MatchSimulator
{
    public function __construct(private Randomizer $rng = new Randomizer())
    {
    }

    /**
     * Simulate match. Both teams get an expected-goals value based-on
     * strength difference and home advantage.
     *
     * @return array{home:int,away:int}
     */
    public function simulate(int $homeStrength, int $awayStrength, int $homeAdvantage = 3): array
    {
        $diff = ($homeStrength + $homeAdvantage) - $awayStrength;

        $homeXg = max(0.3, 1.4 + $diff * 0.025);
        $awayXg = max(0.3, 1.4 - $diff * 0.025);

        return [
            'home' => $this->poisson($homeXg),
            'away' => $this->poisson($awayXg),
        ];
    }

    /** Knuth's algorithm */
    private function poisson(float $lambda): int
    {
        $L = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= $this->rng->nextFloat();
        } while ($p > $L);

        return $k - 1;
    }
}
