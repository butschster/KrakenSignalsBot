<?php

namespace App\Console\Commands\Kraken;

use App\Entities\Order;
use Butschster\Kraken\Contracts\Client;
use Illuminate\Console\Command;

class UpdateOrdersInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kraken:update-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update information about orders';

    /**
     * @param Client $client
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function handle(Client $client)
    {
        $databaseOrders = Order::open()->get();

        $count = $databaseOrders->count();
        $this->info("Total open orders in database: [{$count}]");

        if ($count > 0) {
            $openOrders = $client->getOpenOrders();
            $closedOrders = $client->getClosedOrders();
            $orders = $openOrders->merge($closedOrders);

            foreach ($databaseOrders as $order) {
                $orderInformation = $orders->get($order->txid);

                if ($orderInformation) {
                    $order->status = $orderInformation->status();
                    $order->save();
                }
            }
        }
    }
}