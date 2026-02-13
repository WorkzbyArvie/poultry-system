<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'consumer_id', 'farm_owner_id', 'subtotal', 'shipping_cost',
        'tax', 'discount', 'total_amount', 'status', 'payment_status', 'payment_method',
        'paymongo_payment_id', 'delivery_type', 'delivery_address', 'delivery_city',
        'delivery_province', 'delivery_postal_code', 'scheduled_delivery_at', 'delivered_at',
        'notes', 'item_count'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'scheduled_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function consumer()
    {
        return $this->belongsTo(User::class, 'consumer_id');
    }

    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function itemsWithProducts()
    {
        return $this->items()->with('product');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function incomeRecord()
    {
        return $this->hasOne(IncomeRecord::class);
    }

    // Query Scopes - Performance Optimized
    public function scopeForConsumer(Builder $query, int|User $consumer)
    {
        $consumerId = $consumer instanceof User ? $consumer->id : $consumer;
        return $query->where('consumer_id', $consumerId);
    }

    public function scopeForFarmOwner(Builder $query, int|FarmOwner $farmOwner)
    {
        $farmOwnerId = $farmOwner instanceof FarmOwner ? $farmOwner->id : $farmOwner;
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing(Builder $query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeDelivered(Builder $query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid(Builder $query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopeByStatus(Builder $query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus(Builder $query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    public function scopeWithItems(Builder $query)
    {
        return $query->with('itemsWithProducts');
    }

    public function scopeRecentFirst(Builder $query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeByDeliveryType(Builder $query, string $type)
    {
        return $query->where('delivery_type', $type);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'payment_status' => 'paid'
        ]);
    }
}
