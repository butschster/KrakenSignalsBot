<?php

namespace App\Services\Kraken\Faker;

use App\Entities\Order;
use Butschster\Kraken\Contracts\Client;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Testing\WithFaker;

class OrderStatus implements Arrayable
{
    use WithFaker;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     * @param array $parameters
     */
    public function __construct(Client $client, array $parameters)
    {
        $this->parameters = $parameters;
        $this->client = $client;

        $this->setUpFaker();
    }

    /**
     * @return array
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function toArray()
    {
        $resultType = $this->faker->randomElement([
            'ok', 'ok', 'ok', 'ok', 'ok', 'ok', 'ok', 'error'
        ]);

        if ($resultType == 'error') {
            return [
                'error' => $this->faker->randomElements([
                    'EAPI:Invalid nonce',
                    'EOrder:Insufficient funds',
                    'EGeneral:Invalid arguments:volume',
                    'EQuery:Unknown asset pair',
                ]),
                'result' => []
            ];
        }

        $txid = $this->getTxId();

        return [
            'error' => [],
            'result' => [
                'descr' => [
                    'order' => sprintf(
                        '%s %s %s',
                        $this->parameters['type'],
                        $this->parameters['volume'],
                        $this->parameters['pair']
                    )
                ],
                'txid' => [
                    $this->getTxId()
                ]
            ]
        ];
    }

    /**
     * @return string
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    private function getTxId(): string
    {
        $openOrders = $this->client->getOpenOrders();
        $closedOrders = $this->client->getClosedOrders();

        $txids = $openOrders->merge($closedOrders)->keys();

        $txid = null;

        do {
            $last = $txids->pop();

            if ($last && !Order::where('txid', $last)->first()) {
                $txid = $last;
            }
        } while (!$txid && $last);

        if (!$txid) {
            $txid = $this->generateRandomString(6) . '-' . $this->generateRandomString(5) . '-' . $this->generateRandomString(6);
        }

        return $txid;
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return strtoupper($randomString);
    }
}