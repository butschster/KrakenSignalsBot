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
        \App\Events\Kraken\TooSmallVolume::class => [
            \App\Listeners\Kraken\LogToSmallVolume::class
        ],
        \App\Events\Imap\MessageProcessing::class => [
            \App\Listeners\Imap\LogMessageProcessingToDatabase::class
        ],
//        \App\Events\Imap\MessageProcessed::class => [
//            \App\Listeners\Imap\LogMessageProcessingToDatabase::class
//        ],
        \App\Events\Imap\MessageFailed::class => [
            \App\Listeners\Imap\LogMessageProcessingToDatabase::class
        ],
        \App\Events\AlertProcessing::class => [

        ],
        \App\Events\ParserFound::class => [
            \App\Listeners\LogInformationAboutParser::class
        ]
    ];

    /**
     * The event observers mappings for the models.
     *
     * @var array
     */
    protected $observers = [
        \App\Entities\Balance::class => [
            \App\Entities\Observers\BalanceChanged::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->registerObservers();
    }

    protected function registerObservers(): void
    {
        foreach ($this->observers as $model => $observers) {
            foreach ($observers as $observer) {
                $model::observe($observer);
            }
        }
    }
}
