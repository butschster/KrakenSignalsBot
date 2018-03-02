<?php

namespace App\Console\Commands\Kraken;

use App\Entities\Order;
use Butschster\Kraken\Contracts\Client;
use Illuminate\Console\Command;

class CheckOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kraken:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check order states';

    /**
     * @param Client $client
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function handle(Client $client)
    {
        $openOrders = $client->getOpenOrders();
        $closedOrders = $client->getClosedOrders();

        $orders = $openOrders->merge($closedOrders);

        $this->table(
            ['Date', 'TXID', 'Status'],
            $orders->map(function ($order) {
                return [
                    'date' => $order->openDate()->format('d.m.Y H:i'),
                    $order->id(),
                    $this->getFormattedStatus($order->status())
                ];
            })->sortByDesc('date')
        );

        $databaseOrders = Order::open()->get();
    }

    private function getFormattedStatus($status)
    {
        if ($status == 'canceled') {
            return "<fg=red>{$status}</>";
        }

        return $status;
    }
}
