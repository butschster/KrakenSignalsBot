<?php

namespace App\Services\Kraken\Objects;

use App\Entities\Alert;

final class OrderStatus
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

        $this->descriptions[] = new OrderStatusDescription(
            array_get($descriptions, 'order'),
            array_get($descriptions, 'close')
        );
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