<?php

namespace App\Providers;

use App\Contracts\OrderManager as OrderManagerContract;
use App\OrderManager;
use App\Services\Kraken\Client as KrakenClient;
use App\Services\Kraken\FakeClient as KrakenFakeClient;
use Butschster\Kraken\Contracts\Client as KrakenClientContract;
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
        $this->app->singleton(KrakenClientContract::class, function () {
            $config = $this->app->make('config')->get('kraken');

            if ($this->app->isLocal()) {
                return new KrakenFakeClient(
                    new \GuzzleHttp\Client(),
                    $config['key'],
                    $config['secret'],
                    $config['otp']
                );
            }

            return new KrakenClient(
                new \GuzzleHttp\Client(),
                $config['key'],
                $config['secret'],
                $config['otp']
            );
        });

        $this->app->singleton(OrderManagerContract::class, function () {
            return new OrderManager(
                $this->app[KrakenClientContract::class],
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
