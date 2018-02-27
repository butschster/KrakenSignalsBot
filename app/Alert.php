<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FAILED = 'failed';

    protected $fillable = ['message_id', 'date', 'pair', 'type', 'volume', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
