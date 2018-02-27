<?php

namespace App\Events\Kraken;

class OrderFailed
{
    /**
     * @var \Exception
     */
    public $exception;

    /**
     * @param \Exception $e
     */
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }
}
