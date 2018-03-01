<?php

namespace App\Http\Controllers\Api;

use App\Entities\Balance;
use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceCollection;

class BalanceController extends Controller
{
    public function index()
    {
        return new BalanceCollection(
            Balance::lastBalance()->get()->sortByDesc('amount')
        );
    }
}
