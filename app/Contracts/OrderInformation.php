<?php

namespace App\Contracts;

interface OrderInformation
{

    /**
     * Get order pair currency information
     *
     * @return string
     */
    public function getPair(): string;

    /**
     * Get order type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get order volume
     *
     * @return float
     */
    public function getVolume(): float;
}