<?php

namespace App\Events;

use App\Contracts\Parser;
use Ddeboer\Imap\Message;

class ParserFound
{
    /**
     * @var Parser
     */
    public $parser;

    /**
     * @var Message
     */
    public $message;

    /**
     * @param Parser $parser
     * @param Message $message
     */
    public function __construct(Parser $parser, Message $message)
    {
        $this->parser = $parser;
        $this->message = $message;
    }
}