<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payroll';

    protected $fillable = [
        'farm_owner_id', 'employee_id', 'processed_by', 'payroll_period',
        'period_start', 'period_end', 'pay_date', 'days_worked', 'hours_worked',
        'overtime_hours', 'basic_pay', 'overtime_pay', 'holiday_pay', 'allowances',
        'bonuses', 'gross_pay', 'sss_deduction', 'philhealth_deduction',
        'pagibig_deduction', 'tax_deduction', 'loan_deduction', 'other_deductions',
        'total_deductions', 'net_pay', 'payment_method', 'status', 'notes'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'pay_date' => 'date',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'basic_pay' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'holiday_pay' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'sss_deduction' => 'decimal:2',
        'philhealth_deduction' => 'decimal:2',
        'pagibig_deduction' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Query Scopes
    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeByEmployee(Builder $query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByPeriod(Builder $query, $startDate, $endDate)
    {
        return $query->where('period_start', '>=', $startDate)
                     ->where('period_end', '<=', $endDate);
    }

    public function scopePending(Builder $query)
    {
        return $query->whereIn('status', ['draft', 'pending']);
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('status', 'paid');
    }

    // Methods
    public function calculateGrossPay(): void
    {
        $this->gross_pay = $this->basic_pay + $this->overtime_pay + 
                          $this->holiday_pay + $this->allowances + $this->bonuses;
        $this->save();
    }

    public function calculateTotalDeductions(): void
    {
        $this->total_deductions = $this->sss_deduction + $this->philhealth_deduction +
                                  $this->pagibig_deduction + $this->tax_deduction +
                                  $this->loan_deduction + $this->other_deductions;
        $this->save();
    }

    public function calculateNetPay(): void
    {
        $this->calculateGrossPay();
        $this->calculateTotalDeductions();
        $this->net_pay = $this->gross_pay - $this->total_deductions;
        $this->save();
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $userId
        ]);
    }

    public function markPaid(): void
    {
        $this->update(['status' => 'paid', 'pay_date' => today()]);
    }
}
