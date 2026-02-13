<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Flock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'batch_name', 'breed_type', 'flock_type',
        'initial_count', 'current_count', 'mortality_count', 'sold_count',
        'date_acquired', 'age_weeks', 'source', 'acquisition_cost',
        'status', 'housing_location', 'notes'
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'acquisition_cost' => 'decimal:2',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function records()
    {
        return $this->hasMany(FlockRecord::class);
    }

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class);
    }

    // Query Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeByType(Builder $query, string $type)
    {
        return $query->where('flock_type', $type);
    }

    // Computed Attributes
    public function getMortalityRateAttribute(): float
    {
        if ($this->initial_count === 0) return 0;
        return round(($this->mortality_count / $this->initial_count) * 100, 2);
    }

    public function getSurvivalRateAttribute(): float
    {
        return 100 - $this->mortality_rate;
    }

    // Methods
    public function recordMortality(int $count, ?string $cause = null): void
    {
        $this->increment('mortality_count', $count);
        $this->decrement('current_count', $count);
    }

    public function recordSale(int $count): void
    {
        $this->increment('sold_count', $count);
        $this->decrement('current_count', $count);
    }
}
