<?php

namespace App\Events\Imap;

class ImapConnectionFailed
{
    /**
     * @var \Exception
     */
    public $exception;

    /**
     * @param  \Exception $exception
     */
    public function __construct($exception)
    {
        $this->exception = $exception;
    }
}
