<?php

namespace App\Jobs\Imap;

use Ddeboer\Imap\Message;
use Illuminate\Foundation\Bus\Dispatchable;

class HandleIncommingMessage
{
    use Dispatchable;

    /**
     * @var Message
     */
    public $message;

    /**
     * Create a new job instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->message->markAsSeen();
    }
}
