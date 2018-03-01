<?php

namespace App\Services\Kraken\Objects;

final class OrderStatusDescription
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
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getClose()
    {
        return $this->close;
    }
}