<?php

if (!function_exists('date_formatted')) {

    /**
     * @param Carbon\Carbon|null $carbon
     *
     * @param string $format
     * @return string
     */
    function date_formatted(Carbon\Carbon $carbon = null, string $format = 'd.m.Y H:i:s')
    {
        return $carbon ? $carbon->format($format) : null;
    }
}