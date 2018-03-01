<?php

namespace App\Http\Controllers;

use App\Entities\Log;

class LogsController extends Controller
{
    public function index()
    {
        $logs = Log::latest()->paginate(10);

        return view('logs.index', compact('logs'));
    }
}
