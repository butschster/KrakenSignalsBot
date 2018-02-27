<?php

namespace Tests\Unit;

use App\Contracts\Services\Kraken\Client;
use App\Contracts\Services\Kraken\Order;
use App\OrderManager;
use App\Services\Kraken\OrderStatus;
use Ddeboer\Imap\MessageInterface;
use Tests\TestCase;
use Mockery as m;

class OrderManagerTest extends TestCase
{
    function test_creates_order_from_plain_message()
    {
        $manager = new OrderManager(
            $kraken = m::mock(Client::class)
        );

        $message = m::mock(MessageInterface::class);

        $message->shouldReceive('getBodyText')->once()->andReturn('20.02.2018 XBTCZEUR BUY 2');
        $kraken->shouldReceive('addOrder')->once()->andReturnUsing(function (Order $order) {
            $this->assertEquals([
                'pair' => 'XBTCZEUR',
                'type' => 'buy',
                'ordertype' => 'market',
                'volume' => 2.0,
                'expiretm' => time() + 600
            ], $order->toArray());

            return new OrderStatus('test_transaction_id');
        });

        $this->assertInstanceOf(OrderStatus::class, $status = $manager->createOrderFromEmail($message));
        $this->assertEquals('test_transaction_id', $status->getTransactionId());
    }
}
