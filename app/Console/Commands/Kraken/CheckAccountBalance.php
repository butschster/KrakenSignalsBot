<?php

namespace App\Console\Commands\Kraken;

use App\Exceptions\Handler;
use App\Jobs\UpdateBalance;
use Butschster\Kraken\Contracts\Client;
use Butschster\Kraken\Objects\BalanceCollection;
use Illuminate\Console\Command;

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

            $this->table(['Currency', 'Amount'], $balances->map(function ($balance) {
                return [
                    $balance->currency(),
                    $this->getFormattedAmount($balance->amount())
                ];
            }));

            $this->storeBalances($balances);
        } catch (\Exception $e) {
            $this->error("Cannot check account balance. [{$e->getMessage()}]");
            $handler->report($e);
        }
    }

    /**
     * @param float $amount
     * @return float|string
     */
    protected function getFormattedAmount(float $amount)
    {
        if ($amount > 0) {
            return "<info>{$amount}</info>";
        }

        if ($amount < 0) {
            return "<fg=red>{$amount}</>";
        }

        return $amount;
    }

    /**
     * @param BalanceCollection $balances
     */
    public function storeBalances(BalanceCollection $balances): void
    {
        dispatch(new UpdateBalance($balances));
    }
}
