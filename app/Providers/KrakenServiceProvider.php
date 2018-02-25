<?php

namespace App\Providers;

use App\Contracts\Services\Kraken\Client as ClientContract;
use App\Services\Kraken\Client;
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
        $this->app->singleton(ClientContract::class, function() {
            $config = $this->app->make('config')->get('services.kraken');

            return new Client(
                new \GuzzleHttp\Client(),
                $config['key'],
                $config['secret'],
                $config['otp']
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
