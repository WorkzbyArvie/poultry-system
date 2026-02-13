<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    private function getFarmOwner()
    {
        return FarmOwner::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $query = Employee::byFarmOwner($farmOwner->id)
            ->select('id', 'employee_id', 'first_name', 'last_name', 'department', 'position', 'hire_date', 'daily_rate', 'status');

        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('last_name')->paginate(20);

        $stats = [
            'total' => Employee::byFarmOwner($farmOwner->id)->count(),
            'active' => Employee::byFarmOwner($farmOwner->id)->active()->count(),
            'total_monthly_salary' => Employee::byFarmOwner($farmOwner->id)->active()->sum('monthly_salary'),
        ];

        return view('farmowner.employees.index', compact('employees', 'stats'));
    }

    public function create()
    {
        return view('farmowner.employees.create');
    }

    public function store(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'employee_id' => 'required|string|max:50|unique:employees,employee_id,NULL,id,farm_owner_id,' . $farmOwner->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'department' => 'required|in:farm_operations,hr,finance,logistics,sales,admin',
            'position' => 'required|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract,seasonal',
            'hire_date' => 'required|date',
            'daily_rate' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'sss_number' => 'nullable|string|max:20',
            'philhealth_number' => 'nullable|string|max:20',
            'pagibig_number' => 'nullable|string|max:20',
            'tin_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['farm_owner_id'] = $farmOwner->id;

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($employee->farm_owner_id !== $farmOwner->id, 403);

        $employee->load([
            'attendance' => fn($q) => $q->latest('work_date')->limit(30),
            'payroll' => fn($q) => $q->latest('period_start')->limit(12),
        ]);

        return view('farmowner.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($employee->farm_owner_id !== $farmOwner->id, 403);

        return view('farmowner.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($employee->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'department' => 'required|in:farm_operations,hr,finance,logistics,sales,admin',
            'position' => 'required|string|max:100',
            'daily_rate' => 'nullable|numeric|min:0',
            'monthly_salary' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'status' => 'required|in:active,on_leave,suspended,terminated,resigned',
            'notes' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($employee->farm_owner_id !== $farmOwner->id, 403);

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee removed.');
    }
}
