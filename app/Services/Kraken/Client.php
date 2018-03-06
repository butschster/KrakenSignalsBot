<?php

namespace App\Services\Kraken;

use App\Entities\Log;

class Client extends \Butschster\Kraken\Client
{
    protected function sendRequest(string $method, array $parameters, bool $isPublic, $headers): array
    {
        Log::message(sprintf('[Method: %s] [Parameters: %s]', $method, json_encode($parameters)), 'Kraken API');

        $response = parent::sendRequest($method, $parameters, $isPublic, $headers);

        Log::message(sprintf('[Method: %s] [Response: %s]', $method, json_encode($response)), 'Kraken API');

        return $response;
    }
}