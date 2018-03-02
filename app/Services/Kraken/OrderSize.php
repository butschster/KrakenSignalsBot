<?php

namespace App\Services\Kraken;

use App\Exceptions\CurrencyMinimalOrderSizeNotFound;
use Butschster\Kraken\Objects\Pair;

class OrderSize
{
    /**
     * Check minimal order size for selected pair
     *
     * @param Pair $pair
     * @param float $volume
     * @return bool
     * @throws CurrencyMinimalOrderSizeNotFound
     */
    public function checkMinimal(Pair $pair, float $volume): bool
    {
        $minimalSize = $this->getMinimal($pair);

        return $minimalSize < $volume;
    }

    /**
     * @param Pair $pair
     * @return \Illuminate\Config\Repository|mixed
     * @throws CurrencyMinimalOrderSizeNotFound
     */
    public function getMinimal(Pair $pair)
    {
        $minimalSize = config('kraken.minimals.' . $pair->base());

        if (is_null($minimalSize)) {
            throw new CurrencyMinimalOrderSizeNotFound("Minimal volume size for currency [{$pair->base()}] not found.");
        }

        return $minimalSize;
    }
}