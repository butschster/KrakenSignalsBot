<?php

namespace App\Providers;

use App\Contracts\Services\Imap\Client as ClientContract;
use App\Services\Imap\Client;
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
            $server = new \Ddeboer\Imap\Server($config['host'], $config['port']);

            $connection = $server->authenticate($config['username'], $config['password']);

            return new Client($connection);
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
