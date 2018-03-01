<?php

namespace App\Http\Resources;

use App\Entities\Balance;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Balance
 */
class BalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'currency' => $this->currency,
            'amount' => $this->amount,
            'last_update' => $this->updated_at->format('d.F.Y H:i')
        ];
    }
}
