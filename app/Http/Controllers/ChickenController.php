<?php

namespace App\Http\Controllers;

use App\Models\ChickenMonitoring;
use Illuminate\Http\Request;

class ChickenController extends Controller
{
    public function index()
    {
        // Fetch all chicken records from laravel.chicken_monitoring
        $chickenRecords = ChickenMonitoring::orderBy('date_logged', 'desc')->get();
        return view('superadmin.chickens', compact('chickenRecords'));
    }
}