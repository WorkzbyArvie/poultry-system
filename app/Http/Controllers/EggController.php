<?php

namespace App\Http\Controllers;

use App\Models\EggMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EggController extends Controller
{
    /**
     * Show all egg monitoring records (superadmin only).
     */
    public function index()
    {
        // Authorize superadmin access
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmin can view egg records.');
        }

        $eggRecords = EggMonitoring::orderBy('date_collected', 'desc')->paginate(20);
        return view('superadmin.eggs', compact('eggRecords'));
    }
}