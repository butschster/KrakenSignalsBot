<?php

namespace App\Contracts;

interface Parser
{
    public function parse(string $string);
}