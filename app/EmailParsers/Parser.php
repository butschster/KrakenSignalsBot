<?php

namespace App\EmailParsers;

use App\Contracts\Parser as ParserContract;
use App\Entities\Log;
use Butschster\Kraken\Contracts\Client;
use Butschster\Kraken\Contracts\Order;
use Butschster\Kraken\Objects\Pair;
use Butschster\Kraken\Objects\Ticker;

abstract class Parser implements ParserContract
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function parseOrderType(string $type): string
    {
        return strtolower($type) == 'buy' ? Order::TYPE_BUY : Order::TYPE_SELL;
    }

    /**
     * @param string $pair
     * @return Pair
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    protected function getPairInformation(string $pair): Pair
    {
        return $this->client->getAssetPairs($pair)->first();
    }

    /**
     * @param string $pair
     * @return Ticker
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    protected function getTickerInformation(string $pair): Ticker
    {
        return $this->client->getTicker($pair)->first();
    }

    /**
     * @param string $message
     */
    protected function log(string $message): void
    {
        Log::message($message);
    }
}