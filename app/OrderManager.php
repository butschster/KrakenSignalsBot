<?php

namespace App;

use App\Contracts\OrderInformation;
use App\Contracts\OrderManager as OrderManagerContract;
use App\Contracts\Services\Kraken\Client;
use App\Contracts\Services\Kraken\Order;
use App\Entities\Alert;
use App\Services\Imap\MessageContentParser;
use App\Services\Kraken\Objects\OrderStatus;
use Ddeboer\Imap\MessageInterface;

class OrderManager implements OrderManagerContract
{
    /**
     * @var Client
     */
    private $kraken;

    /**
     * @param Client $kraken
     */
    public function __construct(Client $kraken)
    {
        $this->kraken = $kraken;
    }

    /**
     * Create new order and send to Kraken
     *
     * @param MessageInterface $message
     * @return OrderStatus
     * @throws Services\Imap\MessageContentParseException
     * @throws Services\Kraken\KrakenApiErrorException
     */
    public function createOrderFromEmail(MessageInterface $message): OrderStatus
    {
        $orderInformation = $this->parseMessageContent($message);

        $alert = $this->createAlert($message, $orderInformation);

        $this->logOrderInformation($orderInformation);

        $status = $this->kraken->addOrder(
            $this->createOrderInstance($orderInformation)
        );

        $status->setAlert($alert);

        return $status;
    }

    /**
     * @param MessageInterface $message
     * @return \App\OrderInformation
     * @throws \App\Services\Imap\MessageContentParseException
     */
    protected function parseMessageContent(MessageInterface $message): OrderInformation
    {
        return (new MessageContentParser)
            ->parse(
                $message->getBodyText() ?: $message->getBodyHtml()
            );
    }

    /**
     * @param MessageInterface $message
     * @param OrderInformation $orderInformation
     * @return Alert
     */
    public function createAlert(MessageInterface $message, OrderInformation $orderInformation): Alert
    {
        return Alert::create([
            'message_id' => $message->getId(),
            'date' => $orderInformation->getDate(),
            'pair' => $orderInformation->getPair(),
            'type' => $orderInformation->getType(),
            'volume' => $orderInformation->getVolume(),
            'status' => Alert::STATUS_NEW
        ]);
    }

    /**
     * @param $orderInformation
     */
    protected function logOrderInformation($orderInformation): void
    {
        \App\Entities\Log::message(sprintf(
            'Available new alert: [%s] %s %s',
            $orderInformation->getPair(),
            $orderInformation->getType(),
            $orderInformation->getVolume()
        ));
    }

    /**
     * @param $orderInformation
     * @return Services\Kraken\Order
     */
    protected function createOrderInstance($orderInformation): Services\Kraken\Order
    {
        $order = new \App\Services\Kraken\Order(
            $orderInformation->getPair(),
            $orderInformation->getType(),
            Order::ORDER_TYPE_MARKET,
            $orderInformation->getVolume()
        );

        $order->setExpireTime(now()->addMinutes(10));

        return $order;
    }
}