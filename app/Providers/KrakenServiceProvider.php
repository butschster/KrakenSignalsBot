<?php

namespace App\Providers;

use App\Contracts\OrderManager as OrderManagerContract;
use App\OrderManager;
use App\Services\Kraken\FakeClient;
use Butschster\Kraken\Contracts\Client;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class KrakenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
            $this->app->singleton(Client::class, function () {
                $config = $this->app->make('config')->get('kraken');

                return new FakeClient(
                    new \GuzzleHttp\Client(),
                    $config['key'],
                    $config['secret'],
                    $config['otp']
                );
            });
        }

        $this->app->singleton(OrderManagerContract::class, function() {
            return new OrderManager(
                $this->app[Client::class],
                $this->app[Dispatcher::class]
            );
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
