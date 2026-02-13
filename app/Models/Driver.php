<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'user_id', 'employee_id', 'driver_code', 'name', 'phone',
        'license_number', 'license_expiry', 'vehicle_type', 'vehicle_plate',
        'vehicle_model', 'status', 'delivery_fee', 'completed_deliveries',
        'total_earnings', 'rating', 'notes'
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'delivery_fee' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    // Query Scopes
    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->where('status', 'available');
    }

    public function scopeActive(Builder $query)
    {
        return $query->whereIn('status', ['available', 'on_delivery']);
    }

    public function scopeOnDelivery(Builder $query)
    {
        return $query->where('status', 'on_delivery');
    }

    // Computed
    public function getAverageDeliveryFeeAttribute(): float
    {
        if ($this->completed_deliveries === 0) return 0;
        return round($this->total_earnings / $this->completed_deliveries, 2);
    }

    public function getIsLicenseExpiringAttribute(): bool
    {
        if (!$this->license_expiry) return false;
        return $this->license_expiry->diffInDays(today()) <= 30;
    }

    // Methods
    public function markAvailable(): void
    {
        $this->update(['status' => 'available']);
    }

    public function markOnDelivery(): void
    {
        $this->update(['status' => 'on_delivery']);
    }

    public function completeDelivery(float $fee): void
    {
        $this->increment('completed_deliveries');
        $this->increment('total_earnings', $fee);
        $this->markAvailable();
    }

    public function updateRating(float $newRating): void
    {
        // Weighted average with completed deliveries
        $totalRating = ($this->rating * $this->completed_deliveries) + $newRating;
        $this->rating = round($totalRating / ($this->completed_deliveries + 1), 2);
        $this->save();
    }
}
