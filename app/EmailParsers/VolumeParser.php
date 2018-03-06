<?php

namespace App\EmailParsers;

use Butschster\Kraken\Contracts\Order as OrderContract;

class VolumeParser extends Parser
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'Pair with order type, volume size and leverage';
    }

    /**
     * @return string
     */
    public function regex(): string
    {
        return '/(?<pair>[A-Z]{6,10}+) *(?<type>BUY|SELL|buy|sell) *(?<volume>\d{1,3}(\.\d+)?)( *leverage\=(?<leverage>\d{1,3}))?/';
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

        $pair = $this->getPairInformation($matches['pair']);
        $type = $this->parseOrderType($matches['type']);

        $this->log(sprintf(
            "Parsed signal [Pair: %s] [Type: %s] [Volume: %s] [Leverage: %s]",
            $pair->name(), $type, $matches['volume'], $matches['leverage'] ?? 'none'
        ));

        $order = new \Butschster\Kraken\Order(
            $pair->name(),
            $this->parseOrderType($matches['type']),
            OrderContract::ORDER_TYPE_MARKET,
            (float) $matches['volume']
        );

        if (!empty($matches['leverage'])) {
            $order->setLeverage($matches['leverage']);
        }

        return $order;
    }
}