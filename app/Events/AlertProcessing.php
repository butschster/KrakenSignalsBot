<?php

namespace App\Events;

use App\Entities\Alert;

class AlertProcessing
{
    /**
     * @var Alert
     */
    public $alert;

    /**
     * @param Alert $alert
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }
}