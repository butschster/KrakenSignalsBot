<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * @param string $message
     * @return mixed
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
