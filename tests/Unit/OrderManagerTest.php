<?php

namespace Tests\Unit;

use App\OrderManager;
use Butschster\Kraken\Contracts\Client;
use Butschster\Kraken\Contracts\Order;
use Butschster\Kraken\Objects\OrderStatus;
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
