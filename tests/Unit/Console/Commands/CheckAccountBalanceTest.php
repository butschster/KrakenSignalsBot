<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\Kraken\CheckAccountBalance;
use Butschster\Kraken\Contracts\Client;
use Butschster\Kraken\Objects\Balance;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

class CheckAccountBalanceTest extends TestCase
{
    use RefreshDatabase;

    function test_store_received_balance()
    {
        $command = new CheckAccountBalance();
        $command->setLaravel($this->app);

        $this->app->instance(Client::class, $client = m::mock(Client::class));

        $testBakances = $this->makeTestBalance([
            'ZBTC' => 1.0,
            'ZUSD' => 3415.8014,
            'ZEUR' => 155.5649,
            'XXBT' => 149.9688412800,
            'XXRP' => 499889.51600000,
        ]);

        $client->shouldReceive('getAccountBalance')->once()->andReturn($testBakances);
        $command->run(new StringInput(''), new NullOutput());

        foreach ($testBakances as $b) {
            $balance = \App\Entities\Balance::where('currency', $b->currency())->first();

            if (!$balance) {
                $this->markTestIncomplete("Balance for currency [{$b->currency()}] not found.");
            }

            $this->assertEquals($b->amount(), $balance->amount);
        }
    }

    function test_update_only_changed_balances()
    {
        $command = new CheckAccountBalance();
        $command->setLaravel($this->app);

        $this->app->instance(Client::class, $client = m::mock(Client::class));

        $testBakances = $this->makeTestBalance([
            'ZBTC' => 1.0,
            'ZUSD' => 3415.8014,
            'ZEUR' => 155.5649,
            'XXBT' => 149.9688412800,
            'XXRP' => 499889.51600000,
        ]);

        $client->shouldReceive('getAccountBalance')->once()->andReturn($testBakances);
        $command->run(new StringInput(''), new NullOutput());

        sleep(1);

        $testBakances = $this->makeTestBalance([
            'ZBTC' => 1.0,
            'ZUSD' => 2415.80140,
            'ZEUR' => 155.5659,
            'XXBT' => 149.9688413800,
            'XXRP' => 499889.51600000,
        ]);

        $command->storeBalances($testBakances);

        foreach ($testBakances as $b) {
            $balance = \App\Entities\Balance::where('currency', $b->currency())->latest()->first();
            $this->assertEquals($b->amount(), $balance->amount);
        }
    }

    /**
     * @param array $currencies
     * @return Collection
     */
    public function makeTestBalance(array $currencies): Collection
    {
        return collect($currencies)->map(function ($amount, $currency) {
            return new Balance($currency, $amount);
        });
    }
}
