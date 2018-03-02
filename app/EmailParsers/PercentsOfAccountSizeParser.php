<?php

namespace App\EmailParsers;

use App\Contracts\Parser;
use App\Entities\Balance;
use App\Entities\Log;
use App\Events\Kraken\TooSmallVolume;
use App\Exceptions\InsufficientVolumeSize;
use App\Exceptions\MessageContentParseException;
use App\Exceptions\NotEnoughMoneyException;
use App\Jobs\UpdateBalance;
use App\Services\Kraken\Order;
use App\Services\Kraken\OrderSize;
use Butschster\Kraken\Contracts\Client;
use Butschster\Kraken\Contracts\Order as OrderContract;
use Butschster\Kraken\Objects\Pair;
use Butschster\Kraken\Objects\Ticker;

class PercentsOfAccountSizeParser implements Parser
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
     * @return string
     */
    public function regex(): string
    {
        return '/(?<pair>[A-Z]{6,10}) *(?<type>BUY|SELL|buy|sell) *(?<percent>[0-9]{1,2}%) *of account/';
    }

    /**
     * Parse Email HTML body and gets trade information
     *
     * @param string $text
     * @return OrderContract
     * @throws InsufficientVolumeSize
     * @throws MessageContentParseException
     * @throws NotEnoughMoneyException
     * @throws \App\Exceptions\CurrencyMinimalOrderSizeNotFound
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function parse(string $text): OrderContract
    {
        preg_match($this->regex(), $text, $matches);

        foreach (['pair', 'type', 'percent'] as $field) {
            if (!isset($matches[$field])) {
                throw new MessageContentParseException();
            }
        }

        $type = $this->parseOrderType($matches['type']);

        $this->log(sprintf(
            "Parsed signal [Pair: %s] [Type: %s] [Percent of balance %s]",
            $matches['pair'], $type, $matches['percent']
        ));

        return new Order(
            $matches['pair'],
            $type,
            OrderContract::ORDER_TYPE_MARKET,
            $this->calculateVolume($matches['pair'], (int) $matches['percent'])
        );
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
     * @param int $percent
     * @return float
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     * @throws NotEnoughMoneyException
     * @throws \App\Exceptions\CurrencyMinimalOrderSizeNotFound
     * @throws InsufficientVolumeSize
     */
    protected function calculateVolume(string $pair, int $percent): float
    {
        $pair = $this->getPairInformation($pair);
        sleep(2);
        $ticker = $this->getTickerInformation($pair->name());
        sleep(2);
        $this->syncBalanceInformation();

        $balance = Balance::where('currency', $pair->quote())->latest()->firstOrFail();

        if ($balance->amount <= 0) {
            throw new NotEnoughMoneyException($pair->quote());
        }

        $volume = (($balance->amount * $percent) / 100) / $ticker->lastClosedPrice();

        $this->log(sprintf(
            "Calculated volume for [Pair: %s] [Currency: %s] [Account balance: %s] [Last closed price: %s] [Volume: %s]",
            $pair->name(), $pair->quote(), $balance->amount, $ticker->lastClosedPrice(), $volume
        ));

        $this->checkMinimalVolumeSize($pair, $volume);

        return $volume;
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

    private function syncBalanceInformation()
    {
        dispatch(new UpdateBalance(
            $this->client->getAccountBalance()
        ));
    }

    /**
     * @param Pair $pair
     * @param float $volume
     * @throws InsufficientVolumeSize
     * @throws \App\Exceptions\CurrencyMinimalOrderSizeNotFound
     */
    protected function checkMinimalVolumeSize(Pair $pair, float $volume): void
    {
        $orderSize = new OrderSize();

        if (!$orderSize->checkMinimal($pair, $volume)) {
            event(new TooSmallVolume($pair, $volume));

            throw new InsufficientVolumeSize();
        }
    }

    /**
     * @param string $message
     */
    protected function log(string $message): void
    {
        Log::message($message);
    }
}