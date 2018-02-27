<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\Kraken\OrderCreated::class => [
            \App\Listeners\Kraken\LogCreatedOrderToDatabase::class
        ],
        \App\Events\Kraken\OrderFailed::class => [
            \App\Listeners\Kraken\LogFailedOrderToDatabase::class
        ],
        \App\Events\Imap\MessageProcessing::class => [
            \App\Listeners\Imap\LogMessageProcessingToDatabase::class
        ],
        \App\Events\Imap\MessageProcessed::class => [
            \App\Listeners\Imap\LogMessageProcessingToDatabase::class
        ],
        \App\Events\Imap\MessageFailed::class => [
            \App\Listeners\Imap\LogMessageProcessingToDatabase::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
