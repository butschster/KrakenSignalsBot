<?php

namespace App\Console\Commands;

use App\Entities\Alert;
use Illuminate\Console\Command;

class MarkOutdatedAlertsAsfailed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:prune-outdated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all outdated alerts as failed';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Alert::where('status', Alert::STATUS_NEW)
            ->where('created_at', '<', now()->subMinutes(30))
            ->update([
                'status' => Alert::STATUS_FAILED
            ]);
    }
}
