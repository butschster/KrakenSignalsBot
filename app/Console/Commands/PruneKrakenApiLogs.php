<?php

namespace App\Console\Commands;

use App\Entities\Log;
use Illuminate\Console\Command;

class PruneKrakenApiLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kraken:prune-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Kraken API logs.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::table((new Log)->getTable())->where('type', 'Kraken API')->delete();
    }
}
