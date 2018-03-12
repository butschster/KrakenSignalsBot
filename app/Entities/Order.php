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
     * @param $query
     * @return mixed
     */
    public function scopeOpen($query)
    {
        return $query->where('status', static::STATUS_OPEN);
    }
}
