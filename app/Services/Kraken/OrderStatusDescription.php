<?php

namespace App\Services\Kraken;

class OrderStatusDescription
{
    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $close;

    /**
     * @param string $order
     * @param string|null $close
     */
    public function __construct(string $order, string $close = null)
    {
        $this->order = $order;
        $this->close = $close;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getClose(): string
    {
        return $this->close;
    }
}