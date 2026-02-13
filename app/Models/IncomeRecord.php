<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class IncomeRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'recorded_by', 'order_id', 'income_number', 'category',
        'description', 'customer_name', 'customer_contact', 'amount', 'tax_amount',
        'discount_amount', 'total_amount', 'income_date', 'payment_status',
        'payment_method', 'reference_number', 'receipt_url', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'income_date' => 'date',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Query Scopes
    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeByCategory(Builder $query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('income_date', [$startDate, $endDate]);
    }

    public function scopeReceived(Builder $query)
    {
        return $query->where('payment_status', 'received');
    }

    public function scopePending(Builder $query)
    {
        return $query->whereIn('payment_status', ['pending', 'partial']);
    }

    public function scopeThisMonth(Builder $query)
    {
        return $query->whereMonth('income_date', now()->month)
                     ->whereYear('income_date', now()->year);
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('income_date', today());
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($income) {
            if (!$income->income_number) {
                $income->income_number = 'INC-' . date('Ymd') . '-' . str_pad(static::count() + 1, 5, '0', STR_PAD_LEFT);
            }
            $income->total_amount = $income->amount + ($income->tax_amount ?? 0) - ($income->discount_amount ?? 0);
        });
    }
}
