<?php

namespace App\Entities\Observers;

use App\Entities\Balance;
use App\Events\Kraken\BalanceChanged as BalanceChangedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class BalanceChanged
{
    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * @param Balance $balance
     */
    public function updated(Balance $balance)
    {
        $this->events->dispatch(
            new BalanceChangedEvent($balance)
        );
    }
}