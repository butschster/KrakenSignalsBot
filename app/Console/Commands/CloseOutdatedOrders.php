<?php

namespace App\Console\Commands;

use App\Entities\Order;
use Illuminate\Console\Command;

class CloseOutdatedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:prune-outdated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all outdated orders as outdated';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Order::where('status', Order::STATUS_OPEN)
            ->where('created_at', '<', now()->subDay())
            ->update([
                'status' => Order::STATUS_OUTDATED
            ]);
    }
}
