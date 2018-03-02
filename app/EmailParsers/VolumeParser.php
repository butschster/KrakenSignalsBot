<?php

namespace App\EmailParsers;

use App\Contracts\Parser;
use App\Exceptions\MessageContentParseException;
use Butschster\Kraken\Contracts\Order as OrderContract;
use App\Services\Kraken\Order;

class VolumeParser implements Parser
{
    /**
     * @return string
     */
    public function regex(): string
    {
        return '/(?<date>\d{2}\.\d{2}\.\d{4}) *(?<pair>[A-Z]{6,10}+) *(?<type>BUY|SELL) *(?<volume>\d{1,3})/';
    }

    /**
     * Parse Email HTML body and gets trade information
     *
     * @param string $text
     * @return OrderContract
     * @throws MessageContentParseException
     */
    public function parse(string $text): OrderContract
    {
        preg_match($this->regex(), $text, $matches);

        foreach (['pair', 'type', 'volume'] as $field) {
            if (!isset($matches[$field])) {
                throw new MessageContentParseException();
            }
        }

        return new \Butschster\Kraken\Order(
            $matches['pair'],
            $this->parseOrderType($matches['type']),
            OrderContract::ORDER_TYPE_MARKET,
            (float)$matches['volume']
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
}