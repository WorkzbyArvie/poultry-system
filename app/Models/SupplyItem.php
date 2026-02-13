<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class SupplyItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'supplier_id', 'sku', 'name', 'description', 'category',
        'brand', 'unit', 'quantity_on_hand', 'minimum_stock', 'reorder_point',
        'unit_cost', 'selling_price', 'expiration_date', 'batch_number',
        'storage_location', 'status', 'notes'
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'expiration_date' => 'date',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // Query Scopes
    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeLowStock(Builder $query)
    {
        return $query->whereColumn('quantity_on_hand', '<=', 'reorder_point');
    }

    public function scopeOutOfStock(Builder $query)
    {
        return $query->where('quantity_on_hand', '<=', 0);
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30)
    {
        return $query->whereNotNull('expiration_date')
                     ->where('expiration_date', '<=', now()->addDays($days))
                     ->where('expiration_date', '>=', today());
    }

    public function scopeExpired(Builder $query)
    {
        return $query->whereNotNull('expiration_date')
                     ->where('expiration_date', '<', today());
    }

    public function scopeByCategory(Builder $query, string $category)
    {
        return $query->where('category', $category);
    }

    // Computed
    public function getInventoryValueAttribute(): float
    {
        return $this->quantity_on_hand * $this->unit_cost;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_on_hand <= $this->reorder_point;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiration_date) return null;
        return today()->diffInDays($this->expiration_date, false);
    }

    // Methods
    public function adjustStock(float $quantity, string $type, int $userId, ?string $reason = null): StockTransaction
    {
        $before = $this->quantity_on_hand;
        $this->quantity_on_hand += ($type === 'stock_in' ? $quantity : -$quantity);
        $this->updateStatus();
        $this->save();

        return $this->stockTransactions()->create([
            'farm_owner_id' => $this->farm_owner_id,
            'recorded_by' => $userId,
            'transaction_type' => $type,
            'quantity' => abs($quantity),
            'quantity_before' => $before,
            'quantity_after' => $this->quantity_on_hand,
            'transaction_date' => today(),
            'reason' => $reason,
        ]);
    }

    public function updateStatus(): void
    {
        if ($this->is_expired) {
            $this->status = 'expired';
        } elseif ($this->quantity_on_hand <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->is_low_stock) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }
    }
}
