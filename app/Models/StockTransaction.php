<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_owner_id', 'supply_item_id', 'supplier_id', 'recorded_by',
        'transaction_type', 'quantity', 'unit_cost', 'total_cost',
        'quantity_before', 'quantity_after', 'reference_number', 'invoice_number',
        'transaction_date', 'reason', 'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function supplyItem()
    {
        return $this->belongsTo(SupplyItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Query Scopes
    public function scopeByFarmOwner(Builder $query, int $farmOwnerId)
    {
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeStockIn(Builder $query)
    {
        return $query->where('transaction_type', 'stock_in');
    }

    public function scopeStockOut(Builder $query)
    {
        return $query->where('transaction_type', 'stock_out');
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('transaction_date', today());
    }
}
