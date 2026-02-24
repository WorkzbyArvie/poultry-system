<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DepartmentUserController extends Controller
{
    private array $departmentRoles = [
        'farm_operations',
        'hr',
        'finance',
        'logistics',
        'sales',
        'admin',
    ];

    public function index()
    {
        $users = User::query()
            ->whereIn('role', $this->departmentRoles)
            ->select('id', 'name', 'email', 'role', 'status', 'created_at')
            ->latest('created_at')
            ->paginate(20);

        return view('hr.users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->departmentRoles;

        return view('hr.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $this->departmentRoles),
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'] ?? 'active',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('hr.users.index')->with('success', 'Department user created successfully.');
    }
}
