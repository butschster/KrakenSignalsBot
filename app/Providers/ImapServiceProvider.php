<?php

namespace App\Providers;

use App\Contracts\Services\Imap\Client as ClientContract;
use App\Services\Imap\Client;
use Ddeboer\Imap\Server;
use Illuminate\Support\ServiceProvider;

class ImapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ClientContract::class, function () {
            $config = $this->app->make('config')->get('mail.imap');

            return new Client(
                new Server($config['host'], $config['port']),
                $config['username'],
                $config['password']
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
