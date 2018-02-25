<?php

namespace App\Contracts\Services\Kraken;

use Illuminate\Contracts\Support\Arrayable;

interface Order extends Arrayable
{
    const TYPE_BUY = 'buy';
    const TYPE_SELL = 'sell';

    const ORDER_TYPE_MARKET = 'market';
    const ORDER_TYPE_LIMIT = 'limit'; // price = limit price
    const ORDER_TYPE_STOP_LOSS = 'stop-loss'; // price = limit price
    const ORDER_TYPE_TAKE_PROFIT = 'take-profit'; // price = take profit price
    const ORDER_TYPE_STOP_LOSS_PROFIT = 'stop-loss-profit'; // price = stop loss price, price2 = take profit price
    const ORDER_TYPE_STOP_LOSS_PROFIT_LIMIT = 'stop-loss-profit-limit'; // price = stop loss price, price2 = take profit price
    const ORDER_TYPE_STOP_LOSS_LIMIT = 'stop-loss-limit'; // price = stop loss trigger price, price2 = triggered limit price
    const ORDER_TYPE_TAKE_PROFIT_LIMIT = 'take-profit-limit'; // price = take profit trigger price, price2 = triggered limit price
    const ORDER_TYPE_TRAILING_STOP = 'trailing-stop'; // price = trailing stop offset
    const ORDER_TYPE_TRAILING_STOP_LIMIT = 'trailing-stop-limit'; // price = trailing stop offset, price2 = triggered limit offset
    const ORDER_TYPE_STOP_LOSS_AND_LIMIT = 'stop-loss-and-limit'; // price = stop loss price, price2 = limit price
    const ORDER_TYPE_SETTLE_POSITION = 'settle-position';

    const FLAG_VIQC = 'viqc'; // volume in quote currency (not available for leveraged orders)
    const FLAG_FCIB = 'fcib'; // prefer fee in base currency
    const FLAG_FCIQ = 'fciq'; // prefer fee in quote currency
    const FLAG_NOMPP = 'nompp'; // no market price protection
    const FLAG_POST = 'post'; // post only order (available when ordertype = limit)
}