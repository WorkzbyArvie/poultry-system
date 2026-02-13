<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'farm_owner_id', 'document_type', 'document_name', 'file_path',
        'file_name', 'mime_type', 'file_size', 'status', 'rejection_reason',
        'expiry_date', 'verified_at', 'verified_by'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function farmOwner()
    {
        return $this->belongsTo(FarmOwner::class);
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Query Scopes
    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified(Builder $query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected(Builder $query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired(Builder $query)
    {
        return $query->where('expiry_date', '<', now())->where('status', 'verified');
    }

    public function scopeByUser(Builder $query, int|User $user)
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $query->where('user_id', $userId);
    }

    public function scopeByFarmOwner(Builder $query, int|FarmOwner $farmOwner)
    {
        $farmOwnerId = $farmOwner instanceof FarmOwner ? $farmOwner->id : $farmOwner;
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeByType(Builder $query, string $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeNeedsVerification(Builder $query)
    {
        return $query->where('status', 'pending')->orWhere('status', 'expired');
    }

    // Methods
    public function markAsVerified(User $verifiedBy)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy->id
        ]);
    }

    public function reject(string $reason)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
