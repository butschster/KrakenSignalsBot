<?php

namespace App\Listeners\Kraken;

use App\Entities\Log;
use App\Events\Kraken\TooSmallVolume;
use App\Services\Kraken\OrderSize;

class LogToSmallVolume
{

    /**
     * @param TooSmallVolume $event
     * @throws \App\Exceptions\CurrencyMinimalOrderSizeNotFound
     */
    public function handle(TooSmallVolume $event)
    {
        $orderSize = new OrderSize();

        Log::message(sprintf(
            "Insufficient volume size for [Currency: %s] [Volume: %s] [Minimal: %s]",
            $event->pair->base(), $event->volume, $orderSize->getMinimal($event->pair)
        ), 'error');
    }
}