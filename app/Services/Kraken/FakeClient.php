<?php

namespace App\Services\Kraken;

use App\Services\Kraken\Faker\OrderStatus;
use Butschster\Kraken\Client;
use Butschster\Kraken\Exceptions\KrakenApiErrorException;

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
            return (new OrderStatus($this, $parameters))->toArray();
        }

        if ($method == 'Balance') {
            return [
                'result' => [
                    'KFEE' => -12.4800,
                    'XXRP' => 0,
                    'XLTC' => 500.0002,
                    'XXLM' => 0.8408,
                    'XETH' => 0.0000,
                    'XXMR' => 0.0036,
                    'BCH' => 100.0000,
                    'XXBT' => 0.0002,
                    'ZUSD' => 29872.5767,
                ]
            ];
        }

        return parent::sendRequest($method, $parameters, $isPublic, $headers);
    }
}