<?php

namespace App\Services\Kraken;

use App\Alert;

class OrderStatus
{
    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var OrderStatusDescription[]
     */
    protected $descriptions;

    /**
     * @var Alert
     */
    protected $alert;

    /**
     * @param string $transactionId
     * @param array $descriptions
     */
    public function __construct(string $transactionId, array $descriptions = [])
    {
        $this->transactionId = $transactionId;
        $this->descriptions = [];

        foreach ($descriptions as $description) {
            $this->descriptions[] = new OrderStatusDescription(
                array_get($description, 'order'),
                array_get($description, 'close')
            );
        }
    }

    /**
     * @param Alert $alert
     */
    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @return OrderStatusDescription[]
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    /**
     * @return Alert
     */
    public function getAlert(): Alert
    {
        return $this->alert;
    }
}