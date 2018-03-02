<?php

namespace App\Events\Kraken;

use App\Entities\Alert;
use Butschster\Kraken\Objects\OrderStatus;

class OrderCreated
{
    /**
     * @var OrderStatus
     */
    public $status;

    /**
     * @var Alert
     */
    public $alert;

    /**
     * @param OrderStatus $status
     */
    public function __construct(Alert $alert, OrderStatus $status)
    {
        $this->status = $status;
        $this->alert = $alert;
    }
}
