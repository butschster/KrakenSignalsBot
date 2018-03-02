<?php

namespace App\Contracts;

use Butschster\Kraken\Contracts\Order;

interface Parser
{
    public function regex(): string;

    public function parse(string $string): Order;
}