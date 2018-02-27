<?php

namespace App\Contracts;

use Carbon\Carbon;

interface OrderInformation
{
    /**
     * Get order date
     *
     * @return Carbon
     */
    public function getDate(): Carbon;

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
     * @return int
     */
    public function getVolume(): int;
}