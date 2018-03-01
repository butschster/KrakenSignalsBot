<?php

namespace App\Http\Resources;

use App\Entities\Balance;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BalanceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (Balance $balance) {
            return new BalanceResource($balance);
        });
    }
}
