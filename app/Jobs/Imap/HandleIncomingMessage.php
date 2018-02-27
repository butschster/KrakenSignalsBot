<?php

namespace App\Jobs\Imap;

use App\Contracts\Services\Imap\Client;
use App\Contracts\OrderManager;
use App\Events\Kraken\OrderCreated;
use App\Events\Kraken\OrderFailed;
use App\Exceptions\Handler;
use App\Log;
use Ddeboer\Imap\MessageInterface as Message;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Psr\Log\LoggerInterface;

class HandleIncomingMessage
{
    use Dispatchable;

    /**
     * @var Message
     */
    public $messageId;

    /**
     * Create a new job instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->messageId = $message->getNumber();
    }

    /**
     * @param Client $client
     * @param OrderManager $orderManager
     * @param Handler $handler
     * @param Dispatcher $dispatcher
     */
    public function handle(Client $client, OrderManager $orderManager, Handler $handler, Dispatcher $dispatcher)
    {
        $client->connect();

        try {
            $message = $client->getMessage($this->messageId, Client::MAILBOX_INBOX);
        } catch (\Exception $e) {
            $handler->report($e);
            return;
        }

        Log::message('Processing new alert. Message: '. $message->getId());

        try {
            $status = $orderManager->createOrderFromEmail($message);

            $dispatcher->dispatch(
                new OrderCreated($status)
            );

            return $client->moveToProcessed($message);
        } catch (\Exception $e) {

            $dispatcher->dispatch(
                new OrderFailed($e)
            );

            $handler->report($e);
        }

        return $client->moveToFailed($message);
    }
}
