<?php

namespace App\EmailParsers;

use App\Entities\Balance;
use App\Events\Kraken\TooSmallVolume;
use App\Exceptions\InsufficientVolumeSize;
use App\Jobs\UpdateBalance;
use App\Services\Kraken\Order;
use App\Services\Kraken\OrderSize;
use Butschster\Kraken\Contracts\Order as OrderContract;
use Butschster\Kraken\Objects\Pair;

class PercentsOfAccountSizeParser extends Parser
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'Pair with order type and percents of balance';
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
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function parse(string $text): OrderContract
    {
        preg_match($this->regex(), $text, $matches);

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
     * @param string $pair
     * @param int $percent
     * @return float
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    protected function calculateVolume(string $pair, int $percent): float
    {
        $pair = $this->getPairInformation($pair);
        sleep(2);
        $ticker = $this->getTickerInformation($pair->name());
        sleep(2);
        $this->syncBalanceInformation();

        $balance = Balance::where('currency', $pair->quote())->latest()->firstOrFail();

//        if ($balance->amount <= 0) {
//            throw new NotEnoughMoneyException($pair->quote());
//        }

        $volume = (($balance->amount * $percent) / 100) / $ticker->lastClosedPrice();

        $this->log(sprintf(
            "Calculated volume for [Pair: %s] [Currency: %s] [Account balance: %s] [Last closed price: %s] [Volume: %s]",
            $pair->name(), $pair->quote(), $balance->amount, $ticker->lastClosedPrice(), $volume
        ));

//        $this->checkMinimalVolumeSize($pair, $volume);

        return $volume;
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
}