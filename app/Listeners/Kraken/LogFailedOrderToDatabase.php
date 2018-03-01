<?php

namespace App\Listeners\Kraken;

use App\Events\Kraken\OrderFailed;
use App\Entities\Log;

class LogFailedOrderToDatabase
{
    /**
     * @param OrderFailed $event
     */
    public function handle(OrderFailed $event)
    {
        Log::message('Order failed: ' . $event->exception->getMessage(), 'error');
    }
}
