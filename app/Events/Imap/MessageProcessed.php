<?php

namespace App\Events\Imap;

use Ddeboer\Imap\MessageInterface as Message;

class MessageProcessed
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
