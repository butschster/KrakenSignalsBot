<?php

namespace App\Contracts\Services\Kraken;

use App\Services\Kraken\Balance;
use App\Services\Kraken\KrakenApiErrorException;
use App\Services\Kraken\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface Client
{
    /**
     * Make API call
     *
     * @param string $method
     * @param array $parameters
     * @param bool $isPublic
     * @return array
     *
     * @throws KrakenApiErrorException
     */
    public function request(string $method, array $parameters = [], bool $isPublic = true): array;

    /**
     * Get account balance
     *
     * @return Collection|Balance[]
     * @throws KrakenApiErrorException
     */
    public function getAccountBalance(): Collection;

    /**
     * Get trade balance
     *
     * @return array
     * @throws KrakenApiErrorException
     */
    public function getTradeBalance(): array;

    /**
     * Add standard order
     *
     * @param Order $order
     * @return OrderStatus
     * @throws KrakenApiErrorException
     */
    public function addOrder(Order $order): OrderStatus;

    /**
     * Cancel open order
     *
     * @param string $transactionId
     * @return array
     * @throws KrakenApiErrorException
     */
    public function cancelOrder(string $transactionId): array;

    /**
     * Get open orders
     *
     * @param bool $trades Whether or not to include trades in output
     * @return array
     * @throws KrakenApiErrorException
     */
    public function getOpenOrders(bool $trades = false): array;

    /**
     * Get closed orders
     *
     * @param bool $trades Whether or not to include trades in output
     * @param Carbon|null $start Starting date
     * @param Carbon|null $end Ending date
     * @return array
     * @throws KrakenApiErrorException
     */
    public function getClosedOrders(bool $trades = false, Carbon $start = null, Carbon $end = null): array;
}