<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'user_id', 'farm_owner_id', 'created_by', 'staff_role', 'status',
        'permissions', 'assigned_at', 'last_activity_at'
    ];

    protected $casts = [
        'permissions' => 'json',
        'assigned_at' => 'datetime',
        'last_activity_at' => 'datetime',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Query Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole(Builder $query, string $role)
    {
        return $query->where('staff_role', $role);
    }

    public function scopeByFarmOwner(Builder $query, int|FarmOwner $farmOwner)
    {
        $farmOwnerId = $farmOwner instanceof FarmOwner ? $farmOwner->id : $farmOwner;
        return $query->where('farm_owner_id', $farmOwnerId);
    }

    public function scopeSuperAdmins(Builder $query)
    {
        return $query->where('staff_role', 'super_admin')->where('status', 'active');
    }

    public function scopeWithUser(Builder $query)
    {
        return $query->with('user:id,name,email,phone');
    }

    // Methods
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) return false;
        return in_array($permission, $this->permissions);
    }

    public function grantPermission(string $permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    public function revokePermission(string $permission)
    {
        if ($this->permissions) {
            $permissions = array_filter($this->permissions, fn($p) => $p !== $permission);
            $this->update(['permissions' => array_values($permissions)]);
        }
    }
}
