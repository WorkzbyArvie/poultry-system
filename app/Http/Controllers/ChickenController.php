<?php

namespace App\Http\Controllers;

use App\Models\ChickenMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChickenController extends Controller
{
    /**
     * Show all chicken monitoring records (superadmin only).
     */
    public function index()
    {
        // Authorize superadmin access
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmin can view chicken records.');
        }

        $chickenRecords = ChickenMonitoring::orderBy('date_logged', 'desc')->paginate(20);
        return view('superadmin.chickens', compact('chickenRecords'));
    }
}