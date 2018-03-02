<?php

namespace App\Services\Kraken;

use Illuminate\Contracts\Support\Jsonable;

class Order extends \Butschster\Kraken\Order implements Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->toArray());
    }
}