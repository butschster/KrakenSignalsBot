<?php

namespace App;

use App\Contracts\OrderManager as OrderManagerContract;
use App\Contracts\Parser;
use App\Entities\Alert;
use App\Events\AlertProcessing;
use App\Events\Kraken\OrderCreated;
use Butschster\Kraken\Contracts\Client;
use Butschster\Kraken\Contracts\Order as OrderContract;
use Butschster\Kraken\Objects\OrderStatus;
use Ddeboer\Imap\MessageInterface;
use Illuminate\Contracts\Events\Dispatcher;

class OrderManager implements OrderManagerContract
{
    /**
     * @var Client
     */
    private $kraken;

    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * @param Client $kraken
     * @param Dispatcher $events
     */
    public function __construct(Client $kraken, Dispatcher $events)
    {
        $this->kraken = $kraken;
        $this->events = $events;
    }

    /**
     * Create new order and send to Kraken
     *
     * @param MessageInterface $message
     * @param Parser $parser
     * @return OrderStatus
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function createOrderFromEmail(MessageInterface $message, Parser $parser): OrderStatus
    {
        $order = $parser->parse(
            $message->getBodyText() ?: $message->getBodyHtml()
        );

        $order->setExpireTime(now()->addMinutes(10));

        $alert = $this->storeAlert($message, $order);

        $this->events->dispatch(
            new AlertProcessing($alert)
        );

        $status = $this->kraken->addOrder($order);

        $this->events->dispatch(
            new OrderCreated($alert, $status)
        );

        return $status;
    }


    /**
     * @param MessageInterface $message
     * @param OrderContract $order
     * @return Alert
     */
    public function storeAlert(MessageInterface $message, OrderContract $order): Alert
    {
        $orderArray = $order->toArray();

        return Alert::create([
            'message_id' => $message->getId(),
            'date' => now(),
            'pair' => $orderArray['pair'],
            'type' => $orderArray['type'],
            'volume' => $orderArray['volume'],
            'status' => Alert::STATUS_NEW
        ]);
    }
}