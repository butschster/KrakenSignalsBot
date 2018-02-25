<?php

namespace App\Contracts\Services\Kraken;

use App\Services\Kraken\KrakenApiErrorException;

interface Client
{
    /**
     * @param string $method
     * @param array $parameters
     * @param bool $isPublic
     * @return array
     *
     * @throws KrakenApiErrorException
     */
    public function request(string $method, array $parameters = [], bool $isPublic = true): array;
}