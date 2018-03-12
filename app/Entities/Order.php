<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_OUTDATED = 'outdated';

    protected $fillable = ['txid', 'status', 'alert_id'];

    /**
     * @return HasMany
     */
    public function descriptions(): HasMany
    {
        return $this->hasMany(OrderDescription::class);
    }

    /**
     * @return BelongsTo
     */
    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status == static::STATUS_CLOSED;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status == static::STATUS_OPEN;
    }

    /**
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->status == static::STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isOutdated(): bool
    {
        return $this->status == static::STATUS_OUTDATED;
    }

    /**
     * @return bool
     */
    public function needAttention(): bool
    {
        return $this->isCanceled() || $this->isOutdated();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOpen($query)
    {
        return $query->where('status', static::STATUS_OPEN);
    }
}
