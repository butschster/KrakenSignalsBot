<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * @param string $message
     * @param string $type
     * @return Log
     */
    public static function message(string $message, string $type = 'info')
    {
        return static::create([
            'message' => $message,
            'type' => $type
        ]);
    }

    protected $fillable = ['message', 'type'];
}
