<?php

namespace App\Events\Kraken;

use Butschster\Kraken\Objects\Pair;

class TooSmallVolume
{
    /**
     * @var Pair
     */
    public $pair;

    /**
     * @var float
     */
    public $volume;

    /**
     * @var float
     */
    public $minimal;

    /**
     * @param Pair $pair
     * @param float $volume
     */
    public function __construct(Pair $pair, float $volume)
    {
        $this->pair = $pair;
        $this->volume = $volume;
    }
}