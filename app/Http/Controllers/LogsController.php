<?php

namespace App\Http\Controllers;

use App\Log;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $logs = Log::latest()->paginate(10);

        return view('logs.index', compact('logs'));
    }
}
