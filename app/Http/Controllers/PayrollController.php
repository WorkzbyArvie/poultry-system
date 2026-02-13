<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\FarmOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    private function getFarmOwner()
    {
        return FarmOwner::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $farmOwner = $this->getFarmOwner();
        
        $query = Payroll::byFarmOwner($farmOwner->id)
            ->with('employee:id,first_name,last_name,position')
            ->select('id', 'employee_id', 'payroll_number', 'period_start', 'period_end', 'basic_pay', 'net_pay', 'status', 'payment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('period_start', $date->month)
                  ->whereYear('period_start', $date->year);
        }

        $payrolls = $query->latest('period_start')->paginate(20);

        $stats = [
            'pending' => Payroll::byFarmOwner($farmOwner->id)->byStatus('pending')->sum('net_pay'),
            'paid_this_month' => Payroll::byFarmOwner($farmOwner->id)->byStatus('paid')
                ->whereMonth('payment_date', now()->month)->sum('net_pay'),
        ];

        return view('farmowner.payroll.index', compact('payrolls', 'stats'));
    }

    public function create()
    {
        $farmOwner = $this->getFarmOwner();
        $employees = Employee::byFarmOwner($farmOwner->id)
            ->active()
            ->select('id', 'first_name', 'last_name', 'position', 'daily_rate', 'monthly_salary')
            ->get();

        return view('farmowner.payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'days_worked' => 'required|numeric|min:0',
            'basic_pay' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'holiday_pay' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'sss_contribution' => 'nullable|numeric|min:0',
            'philhealth_contribution' => 'nullable|numeric|min:0',
            'pagibig_contribution' => 'nullable|numeric|min:0',
            'tax_withholding' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['farm_owner_id'] = $farmOwner->id;
        $validated['prepared_by'] = Auth::id();

        // Generate payroll number
        $count = Payroll::byFarmOwner($farmOwner->id)->whereYear('created_at', now()->year)->count() + 1;
        $validated['payroll_number'] = 'PAY-' . now()->format('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        // Calculate totals
        $grossPay = ($validated['basic_pay'] ?? 0) 
            + ($validated['overtime_pay'] ?? 0) 
            + ($validated['holiday_pay'] ?? 0) 
            + ($validated['allowances'] ?? 0) 
            + ($validated['bonus'] ?? 0);

        $totalDeductions = ($validated['sss_contribution'] ?? 0)
            + ($validated['philhealth_contribution'] ?? 0)
            + ($validated['pagibig_contribution'] ?? 0)
            + ($validated['tax_withholding'] ?? 0)
            + ($validated['loan_deduction'] ?? 0)
            + ($validated['other_deductions'] ?? 0);

        $validated['gross_pay'] = $grossPay;
        $validated['total_deductions'] = $totalDeductions;
        $validated['net_pay'] = $grossPay - $totalDeductions;

        Payroll::create($validated);

        return redirect()->route('payroll.index')->with('success', 'Payroll record created.');
    }

    public function show(Payroll $payroll)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($payroll->farm_owner_id !== $farmOwner->id, 403);

        $payroll->load(['employee', 'preparedBy', 'approvedBy']);

        return view('farmowner.payroll.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($payroll->farm_owner_id !== $farmOwner->id, 403);

        $payroll->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'Payroll approved.');
    }

    public function markPaid(Request $request, Payroll $payroll)
    {
        $farmOwner = $this->getFarmOwner();
        abort_if($payroll->farm_owner_id !== $farmOwner->id, 403);

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,check,gcash',
            'payment_date' => 'required|date',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $payroll->update([
            'status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'payment_reference' => $validated['payment_reference'],
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'Payroll marked as paid.');
    }

    public function generateBatch(Request $request)
    {
        $farmOwner = $this->getFarmOwner();

        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $periodStart = Carbon::parse($validated['period_start']);
        $periodEnd = Carbon::parse($validated['period_end']);

        $employees = Employee::byFarmOwner($farmOwner->id)
            ->active()
            ->when(!empty($validated['employee_ids']), fn($q) => $q->whereIn('id', $validated['employee_ids']))
            ->get();

        $generated = 0;

        foreach ($employees as $employee) {
            // Get attendance for period
            $attendance = Attendance::where('employee_id', $employee->id)
                ->byDateRange($periodStart, $periodEnd)
                ->whereIn('status', ['present', 'late', 'half_day'])
                ->get();

            $daysWorked = $attendance->where('status', 'present')->count()
                + $attendance->where('status', 'late')->count()
                + ($attendance->where('status', 'half_day')->count() * 0.5);

            $overtimeHours = $attendance->sum('overtime_hours');

            // Calculate pay
            $basicPay = $employee->daily_rate ? ($employee->daily_rate * $daysWorked) : ($employee->monthly_salary / 2);
            $overtimePay = $overtimeHours * (($employee->daily_rate ?? ($employee->monthly_salary / 26)) / 8 * 1.25);

            // Generate payroll
            $count = Payroll::byFarmOwner($farmOwner->id)->whereYear('created_at', now()->year)->count() + 1;
            
            Payroll::create([
                'farm_owner_id' => $farmOwner->id,
                'employee_id' => $employee->id,
                'prepared_by' => Auth::id(),
                'payroll_number' => 'PAY-' . now()->format('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'days_worked' => $daysWorked,
                'overtime_hours' => $overtimeHours,
                'basic_pay' => $basicPay,
                'overtime_pay' => $overtimePay,
                'gross_pay' => $basicPay + $overtimePay,
                'net_pay' => $basicPay + $overtimePay,
            ]);

            $generated++;
        }

        return redirect()->route('payroll.index')->with('success', "{$generated} payroll records generated.");
    }
}
