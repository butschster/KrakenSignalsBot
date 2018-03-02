<?php

namespace App\Jobs;

use App\Entities\Balance as BalanceEntity;
use Butschster\Kraken\Objects\Balance;
use Butschster\Kraken\Objects\BalanceCollection;

class UpdateBalance
{
    /**
     * @var BalanceCollection
     */
    protected $balance;

    /**
     * @param BalanceCollection $balance
     */
    public function __construct(BalanceCollection $balance)
    {
        $this->balance = $balance;
    }

    public function handle(): void
    {
        foreach ($this->balance as $balance) {
            $this->storeBalance($balance);
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