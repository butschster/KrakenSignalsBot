<?php

namespace App\Contracts;

use App\Services\Imap\MessageContentParseException;
use App\Services\Kraken\KrakenApiErrorException;
use App\Services\Kraken\Objects\OrderStatus;
use Ddeboer\Imap\MessageInterface;

interface OrderManager
{
    /**
     * Create new order and send to Kraken
     *
     * @param MessageInterface $message
     * @return OrderStatus
     * @throws MessageContentParseException
     * @throws KrakenApiErrorException
     */
    public function createOrderFromEmail(MessageInterface $message): OrderStatus;
}