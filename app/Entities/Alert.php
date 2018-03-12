<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    const STATUS_NEW = 'new';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FAILED = 'failed';

    protected $fillable = ['message_id', 'date', 'pair', 'type', 'volume', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->status == static::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status == static::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->status == static::STATUS_PROCESSED;
    }
}
