<?php

namespace App\Listeners\Kraken;

use App\Entities\Alert;
use App\Events\Kraken\OrderCreated;
use App\Entities\Log;
use App\Entities\Order;

class LogCreatedOrderToDatabase
{
    /**
     * @param OrderCreated $event
     */
    public function handle(OrderCreated $event)
    {
        $status = $event->status;
        $alert = $event->alert;

        /** @var Order $order */
        $order = Order::create([
            'txid' => $status->getTransactionId(),
            'status' => Order::STATUS_OPEN,
            'alert_id' => $alert->id
        ]);

        $alert->update([
            'status' => Alert::STATUS_PROCESSED
        ]);

        $description = $status->getDescription();
        $order->descriptions()->create([
            'order' => $description->getOrder(),
            'close' => $description->getClose(),
        ]);

        Log::message(sprintf('Order created [%s] with txid [%s]', $order->id, $status->getTransactionId()));
    }
}
