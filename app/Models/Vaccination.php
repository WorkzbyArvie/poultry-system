<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Vaccination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'flock_id', 'administered_by', 'type', 'name', 'brand',
        'batch_number', 'dosage', 'dosage_unit', 'administration_method',
        'date_administered', 'next_due_date', 'birds_treated', 'cost',
        'status', 'notes', 'side_effects'
    ];

    protected $casts = [
        'date_administered' => 'date',
        'next_due_date' => 'date',
        'dosage' => 'decimal:3',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    // Query Scopes
    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeUpcoming(Builder $query, int $days = 7)
    {
        return $query->where('next_due_date', '<=', now()->addDays($days))
                     ->where('next_due_date', '>=', today())
                     ->where('status', '!=', 'completed');
    }

    public function scopeOverdue(Builder $query)
    {
        return $query->where('next_due_date', '<', today())
                     ->whereIn('status', ['scheduled', 'missed']);
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeVaccines(Builder $query)
    {
        return $query->where('type', 'vaccine');
    }

    public function scopeMedications(Builder $query)
    {
        return $query->where('type', 'medication');
    }

    // Methods
    public function markCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function reschedule($newDate): void
    {
        $this->update(['next_due_date' => $newDate, 'status' => 'scheduled']);
    }
}
