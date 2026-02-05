<?php

namespace App\Http\Controllers;

use App\Models\EggMonitoring;
use Illuminate\Http\Request;

class EggController extends Controller
{
    public function index()
    {
        // Fetch all egg records from laravel.egg_monitoring
        $eggRecords = EggMonitoring::orderBy('date_collected', 'desc')->get();
        return view('superadmin.eggs', compact('eggRecords'));
    }
}