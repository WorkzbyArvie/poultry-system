<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class FlockRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id', 'recorded_by', 'record_date', 'mortality_today', 'mortality_cause',
        'feed_consumed_kg', 'water_consumed_liters', 'eggs_collected', 'eggs_broken',
        'average_weight_kg', 'health_status', 'health_notes', 'temperature_celsius',
        'humidity_percent', 'remarks'
    ];

    protected $casts = [
        'record_date' => 'date',
        'feed_consumed_kg' => 'decimal:2',
        'water_consumed_liters' => 'decimal:2',
        'average_weight_kg' => 'decimal:3',
        'temperature_celsius' => 'decimal:1',
        'humidity_percent' => 'decimal:1',
    ];

    // Relationships
    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Query Scopes
    public function scopeByFlock(Builder $query, int $flockId)
    {
        return $query->where('flock_id', $flockId);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('record_date', today());
    }

    public function scopeWithMortality(Builder $query)
    {
        return $query->where('mortality_today', '>', 0);
    }

    // Computed
    public function getEggsNetAttribute(): int
    {
        return $this->eggs_collected - $this->eggs_broken;
    }
}
