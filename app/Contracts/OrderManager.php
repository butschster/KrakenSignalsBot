<?php

namespace App\Contracts;

use App\Exceptions\MessageContentParseException;
use Butschster\Kraken\Exceptions\KrakenApiErrorException;
use Butschster\Kraken\Objects\OrderStatus;
use Ddeboer\Imap\MessageInterface;

interface OrderManager
{
    /**
     * Create new order and send to Kraken
     *
     * @param MessageInterface $message
     * @param Parser $parser
     * @return OrderStatus
     */
    public function createOrderFromEmail(MessageInterface $message, Parser $parser): OrderStatus;
}