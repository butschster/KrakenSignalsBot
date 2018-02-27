<?php

namespace App\Services\Imap;

use App\Contracts\Parser;
use App\Contracts\Services\Kraken\Order;
use App\OrderInformation;
use Carbon\Carbon;

class MessageContentParser implements Parser
{
    /**
     * Parse Email HTML body and gets trade information
     *
     * @param string $text
     * @return OrderInformation
     * @throws MessageContentParseException
     */
    public function parse(string $text): OrderInformation
    {
        preg_match('/(?<date>\d{2}\.\d{2}\.\d{4}) *(?<pair>[A-Z]{6,10}+) *(?<type>BUY|SELL) *(?<volume>\d{1,3})/', $text, $matches);

        foreach (['date', 'pair', 'type', 'volume'] as $field) {
            if (!isset($matches[$field])) {
                throw new MessageContentParseException();
            }
        }

        return new OrderInformation(
            Carbon::createFromFormat('d.m.Y', $matches['date']),
            $matches['pair'],
            $this->parseOrderType($matches['type']),
            (int) $matches['volume']
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