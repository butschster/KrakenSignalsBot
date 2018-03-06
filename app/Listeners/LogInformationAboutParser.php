<?php

namespace App\Listeners;

use App\Entities\Log;
use App\Events\ParserFound;

class LogInformationAboutParser
{
    /**
     * @param ParserFound $event
     */
    public function handle(ParserFound $event)
    {
        Log::message(sprintf(
            'Found parser [%s] for message %s',
            $event->parser->name(),
            $event->message->getId()
        ));
    }
}