<?php

namespace App\Events\Imap;

use Ddeboer\Imap\Message;

class MessageProcessing
{
    /**
     * @var Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }
}
