<?php

namespace App\Jobs\Imap;

use App\Contracts\Parser;
use App\Contracts\Services\Imap\Client;
use App\Contracts\OrderManager;
use App\EmailParsers\PercentsOfAccountSizeParser;
use App\EmailParsers\VolumeParser;
use App\Events\AlertProcessing;
use App\Events\Imap\MessageNotFound;
use App\Events\Kraken\OrderCreated;
use App\Events\Kraken\OrderFailed;
use App\Events\ParserFound;
use App\Exceptions\EmailParserNotFound;
use App\Exceptions\Handler;
use App\Entities\Log;
use Ddeboer\Imap\MessageInterface as Message;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HandleIncomingMessage
{
    use Dispatchable;

    /**
     * @var Message
     */
    public $messageId;

    /**
     * @var array
     */
    protected $parsers = [
        PercentsOfAccountSizeParser::class,
        VolumeParser::class
    ];

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
     * @param Dispatcher $events
     */
    public function handle(Client $client, OrderManager $orderManager, Handler $handler, Dispatcher $events): void
    {
        $client->connect();

        try {
            $message = $client->getMessage($this->messageId, Client::MAILBOX_INBOX);
        } catch (\Exception $e) {
            $handler->report($e);
            $events->dispatch(
                new MessageNotFound($this->messageId, Client::MAILBOX_INBOX)
            );

            return;
        }

        try {
            $parser = $this->getMatchedParser($message);
            $events->dispatch(
                new ParserFound($parser, $message)
            );

            $orderManager->createOrderFromEmail(
                $message, $this->getMatchedParser($message)
            );

            $client->moveToProcessed($message);
            return;

        } catch (\Exception $e) {

            $events->dispatch(
                new OrderFailed($e)
            );

            $handler->report($e);
        }

        $client->moveToFailed($message);
    }

    /**
     * @param Message $message
     * @return Parser
     * @throws EmailParserNotFound
     */
    protected function getMatchedParser(Message $message): Parser
    {
        $text = $message->getBodyText() ?: $message->getBodyHtml();

        $parser = collect($this->parsers)->map(function ($parser) {
            return $this->makeParser($parser);
        })->first(function (Parser $parser) use ($text) {
            return preg_match($parser->regex(), $text);
        });

        if (!$parser) {
            throw new EmailParserNotFound("Parser for [{$text}] not found.");
        }

        return $parser;
    }

    /**
     * @param string $parser
     * @return Parser
     */
    protected function makeParser(string $parser): Parser
    {
        return app()->make($parser);
    }
}
