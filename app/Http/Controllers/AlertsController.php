<?php

namespace App\Http\Controllers;

use App\Alert;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $alerts = Alert::with('order')->latest()->paginate(10);

        return view('alerts.index', compact('alerts'));
    }
}
