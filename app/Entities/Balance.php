<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['currency', 'amount'];

    public function scopeLastBalance($query)
    {
        return $query->whereRaw('balances.created_at = (select max(t2.created_at) from balances as t2 where balances.currency = t2.currency)');
    }
}
