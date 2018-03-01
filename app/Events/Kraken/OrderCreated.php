<?php

namespace App\Events\Kraken;

use App\Services\Kraken\Objects\OrderStatus;

class OrderCreated
{
    /**
     * @var OrderStatus
     */
    public $status;

    /**
     * @param OrderStatus $status
     */
    public function __construct(OrderStatus $status)
    {
        $this->status = $status;
    }
}
