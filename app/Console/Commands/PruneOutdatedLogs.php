<?php

namespace App\Console\Commands;

use App\Entities\Log;
use Illuminate\Console\Command;

class PruneOutdatedLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:prune-outdated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove logs older than 7 days.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::table((new Log)->getTable())->where('created_at', '<', now()->subWeek())->delete();
    }
}
