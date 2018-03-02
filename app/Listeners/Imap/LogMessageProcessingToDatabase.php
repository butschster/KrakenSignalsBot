<?php

namespace App\Listeners\Imap;

use App\Entities\Log;

class LogMessageProcessingToDatabase
{
    /**
     * @param $event
     * @throws \ReflectionException
     */
    public function handle($event)
    {
        Log::message(sprintf(
            'Imap [%s]: %s',
            (new \ReflectionClass($event))->getShortName(),
            $event->message->getId()
        ));
    }
}
