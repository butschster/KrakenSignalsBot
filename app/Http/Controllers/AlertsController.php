<?php

namespace App\Http\Controllers;

use App\Entities\Alert;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    public function index()
    {
        $alerts = Alert::with('order')->latest()->paginate(10);

        return view('alerts.index', compact('alerts'));
    }
}
