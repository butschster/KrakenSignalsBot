<?php

namespace App\Events\Imap;

use Ddeboer\Imap\MessageInterface as Message;

class ExceptionOccurred
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @var \Exception
     */
    public $exception;

    /**
     * @param Message $message
     * @param  \Exception $exception
     */
    public function __construct(Message $message, $exception)
    {
        $this->message = $message;
        $this->exception = $exception;
    }
}
