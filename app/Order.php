<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const STATUS_OPEN = 'open';
    const STATUS_PROCESSED = 'processed';

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
        return $this->hasOne(Alert::class);
    }
}
