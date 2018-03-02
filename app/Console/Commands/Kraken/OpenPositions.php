<?php

namespace App\Console\Commands\Kraken;

use App\Entities\Order;
use Butschster\Kraken\Contracts\Client;
use Illuminate\Console\Command;

class OpenPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kraken:open-positions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get open positions';

    /**
     * @param Client $client
     * @throws \Butschster\Kraken\Exceptions\KrakenApiErrorException
     */
    public function handle(Client $client)
    {
        $result = $client->request('OpenPositions', ['txid' => 'OVNTM7-342S7-O7LSAN'], false);

        dd($result);

    }
}
