<?php

namespace App\Events\Imap;

class MessageNotFound
{
    /**
     * @var string
     */
    public $messageId;

    /**
     * @var string
     */
    public $maildbox;

    /**
     * @param $messageId
     */
    public function __construct($messageId, string $maildbox)
    {
        $this->messageId = $messageId;
        $this->maildbox = $maildbox;
    }
}
