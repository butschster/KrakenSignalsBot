<?php

namespace App\Console\Commands;

use App\Balance as BalanceEntity;
use App\Contracts\Services\Kraken\Client;
use App\Exceptions\Handler;
use App\Services\Kraken\Balance;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CheckAccountBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kraken:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update account balance';

    /**
     * @param Client $client
     * @param Handler $handler
     */
    public function handle(Client $client, Handler $handler)
    {
        try {
            $balances = $client->getAccountBalance();
            $this->storeBalances($balances);
        } catch (\Exception $e) {
            $this->error("Cannot check account balance. [{$e->getMessage()}]");
            $handler->report($e);
        }
    }

    /**
     * @param Collection $balances
     */
    public function storeBalances(Collection $balances): void
    {
        foreach ($balances as $balance) {
            $this->storeBalance($balance);

            $this->info(sprintf(
                '[%s][%s] - %s',
                now()->toDateTimeString(),
                $balance->currency(),
                $balance->amount()
            ));
        }
    }

    /**
     * @param Balance $balance
     */
    protected function storeBalance(Balance $balance): void
    {
        $lastBalance = BalanceEntity::where('currency', $balance->currency())->latest()->first();

        if (!$lastBalance || $lastBalance->amount != $balance->amount()) {
            $this->createBalanceRrecord($balance);
        }
    }

    /**
     * @param Balance $balance
     */
    protected function createBalanceRrecord(Balance $balance): void
    {
        BalanceEntity::create([
            'currency' => $balance->currency(),
            'amount' => $balance->amount()
        ]);
    }
}
