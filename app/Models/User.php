<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany, HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'kyc_verified',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'kyc_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Polymorphic Relationships
    public function farmOwner()
    {
        return $this->hasOne(FarmOwner::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    // Consumer Orders
    public function consumerOrders()
    {
        return $this->hasMany(Order::class, 'consumer_id');
    }

    // Documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Subscriptions (legacy)
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->orderByDesc('ends_at');
    }

    // Staff relationships
    public function createdStaff()
    {
        return $this->hasMany(Staff::class, 'created_by');
    }

    public function verifiedDocuments()
    {
        return $this->hasMany(Document::class, 'verified_by');
    }

    // Query Scopes
    public function scopeByRole(Builder $query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified(Builder $query)
    {
        return $query->where('email_verified_at', '!=', null)
                    ->where('kyc_verified', true);
    }

    public function scopeFarmOwners(Builder $query)
    {
        return $query->where('role', 'farm_owner');
    }

    public function scopeConsumers(Builder $query)
    {
        return $query->where('role', 'consumer');
    }

    public function scopeStaff(Builder $query)
    {
        return $query->where('role', 'staff');
    }

    public function scopeSuperAdmins(Builder $query)
    {
        return $query->where('role', 'super_admin');
    }

    public function scopeWithFarmOwner(Builder $query)
    {
        return $query->with('farmOwner');
    }

    public function scopeWithNotifications(Builder $query)
    {
        return $query->with(['notifications' => fn($q) => $q->unread()]);
    }

    public function scopeRecentlyActive(Builder $query, $days = 7)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    // Methods
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isFarmOwner(): bool
    {
        return $this->role === 'farm_owner';
    }

    public function isConsumer(): bool
    {
        return $this->role === 'consumer';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at && $this->kyc_verified;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function markEmailAsVerified()
    {
        if (!$this->email_verified_at) {
            $this->update(['email_verified_at' => now()]);
        }
    }

    public function markPhoneAsVerified()
    {
        if (!$this->phone_verified_at) {
            $this->update(['phone_verified_at' => now()]);
        }
    }

    public function markKYCVerified()
    {
        $this->update(['kyc_verified' => true]);
    }
}
