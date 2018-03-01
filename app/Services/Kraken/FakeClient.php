<?php

namespace App\Services\Kraken;

class FakeClient extends Client
{

    /**
     * @param string $method
     * @param array $parameters
     * @param bool $isPublic
     * @param $headers
     * @return array
     * @throws KrakenApiErrorException
     */
    protected function sendRequest(string $method, array $parameters, bool $isPublic, $headers): array
    {
        if ($method == 'AddOrder') {
            return (new \App\Services\Kraken\Faker\OrderStatus($this, $parameters))->toArray();
        }

        return parent::sendRequest($method, $parameters, $isPublic, $headers);
    }
}