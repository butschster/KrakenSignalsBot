<?php

namespace App\Events\Kraken;

use App\Entities\Balance;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class BalanceChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Balance
     */
    public $balance;

    /**
     * @param Balance $balance
     */
    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }
}
