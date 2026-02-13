<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'plan_type', 'monthly_cost', 'product_limit', 'order_limit',
        'commission_rate', 'status', 'started_at', 'ends_at', 'renewal_at',
        'paymongo_subscription_id', 'paymongo_payment_method_id'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'renewal_at' => 'datetime',
        'monthly_cost' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    // Query Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active')->where('ends_at', '>', now());
    }

    public function scopeExpiring(Builder $query)
    {
        return $query->where('ends_at', '<', now()->addDays(7))->where('status', 'active');
    }

    public function scopeByPlan(Builder $query, string $planType)
    {
        return $query->where('plan_type', $planType);
    }

    public function scopeExpired(Builder $query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeWithFarmOwner(Builder $query)
    {
        return $query->with('farmOwner');
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && (!$this->ends_at || $this->ends_at->isFuture());
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->ends_at) return null;
        return max(0, $this->ends_at->diffInDays(now(), false));
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at && $this->ends_at->isFuture();
    }

    /**
     * Get days remaining on subscription.
     */
    public function daysRemaining(): int
    {
        if (!$this->isActive()) {
            return 0;
        }
        return max(0, (int)now()->diffInDays($this->expires_at, false));
    }
}
