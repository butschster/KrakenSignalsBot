<?php

namespace App;

use App\Contracts\OrderInformation as OrderInformationContract;
use Carbon\Carbon;

class OrderInformation implements OrderInformationContract
{
    /**
     * @var Carbon
     */
    private $date;

    /**
     * @var string
     */
    private $pair;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $volume;

    /**
     * @param Carbon $date
     * @param string $pair
     * @param string $type
     * @param int $volume
     */
    public function __construct(Carbon $date, string $pair, string $type, int $volume)
    {
        $this->date = $date;
        $this->pair = $pair;
        $this->type = $type;
        $this->volume = $volume;
    }

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getPair(): string
    {
        return $this->pair;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getVolume(): int
    {
        return $this->volume;
    }
}