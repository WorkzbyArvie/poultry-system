<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_owner_id', 'sku', 'name', 'description', 'category', 'status',
        'quantity_available', 'quantity_sold', 'price', 'cost_price', 'attributes',
        'unit', 'minimum_order', 'discount_percentage', 'image_url', 'image_urls',
        'view_count', 'favorite_count', 'average_rating', 'review_count', 'published_at'
    ];

    protected $casts = [
        'attributes' => 'json',
        'image_urls' => 'json',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Reviews relationship (uncomment when Review model is implemented)
    // public function reviews()
    // {
    //     return $this->morphMany(Review::class, 'reviewable');
    // }

    // Query Scopes - Performance Optimized
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active')->where('quantity_available', '>', 0);
    }

    public function scopeByCategory(Builder $query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByFarmOwner(Builder $query, int|FarmOwner $farmOwner)
    {
        $farmOwnerId = $farmOwner instanceof FarmOwner ? $farmOwner->id : $farmOwner;
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->where('status', 'active')->where('quantity_available', '>', 0);
    }

    public function scopePopular(Builder $query)
    {
        return $query->orderByDesc('view_count');
    }

    public function scopeTopRated(Builder $query)
    {
        return $query->whereNotNull('average_rating')->orderByDesc('average_rating');
    }

    public function scopeWithFarmOwner(Builder $query)
    {
        return $query->with('farmOwner:id,farm_name,average_rating');
    }

    public function scopeSearchByName(Builder $query, string $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
    }

    public function scopeByPriceRange(Builder $query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeOutOfStock(Builder $query)
    {
        return $query->where('quantity_available', '<=', 0);
    }

    public function scopePublished(Builder $query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    // Accessors
    public function getDiscountedPriceAttribute(): float
    {
        return $this->price * (1 - $this->discount_percentage / 100);
    }

    public function getIsFavoriteAttribute(): bool
    {
        return $this->favorite_count > 0;
    }
}
