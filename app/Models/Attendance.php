<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'farm_owner_id', 'employee_id', 'work_date', 'time_in', 'time_out',
        'break_start', 'break_end', 'hours_worked', 'overtime_hours', 'late_minutes',
        'undertime_minutes', 'status', 'leave_type', 'notes', 'approved_by'
    ];

    protected $casts = [
        'work_date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'late_minutes' => 'decimal:2',
        'undertime_minutes' => 'decimal:2',
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function scopeByDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('work_date', today());
    }

    public function scopePresent(Builder $query)
    {
        return $query->whereIn('status', ['present', 'late']);
    }

    public function scopeAbsent(Builder $query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeOnLeave(Builder $query)
    {
        return $query->where('status', 'on_leave');
    }

    // Methods
    public function clockIn(): void
    {
        $this->update([
            'time_in' => now()->format('H:i:s'),
            'status' => 'present'
        ]);
    }

    public function clockOut(): void
    {
        $this->update([
            'time_out' => now()->format('H:i:s')
        ]);
        $this->calculateHoursWorked();
    }

    public function calculateHoursWorked(): void
    {
        if (!$this->time_in || !$this->time_out) return;

        $timeIn = \Carbon\Carbon::parse($this->time_in);
        $timeOut = \Carbon\Carbon::parse($this->time_out);
        $breakMinutes = 0;

        if ($this->break_start && $this->break_end) {
            $breakStart = \Carbon\Carbon::parse($this->break_start);
            $breakEnd = \Carbon\Carbon::parse($this->break_end);
            $breakMinutes = $breakStart->diffInMinutes($breakEnd);
        }

        $totalMinutes = $timeIn->diffInMinutes($timeOut) - $breakMinutes;
        $this->hours_worked = round($totalMinutes / 60, 2);
        
        // Calculate overtime (anything over 8 hours)
        $this->overtime_hours = max(0, $this->hours_worked - 8);
        $this->save();
    }
}
