<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     * We use the 'laravel' schema prefix to match your Supabase setup.
     */
    protected $table = 'laravel.app_users';

    /**
     * Disable timestamps because your 'app_users' table 
     * does not have created_at and updated_at columns.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * Includes 'full_name' and 'username' to satisfy table constraints.
     */
    protected $fillable = [
        'full_name',
        'username', 
        'email',
        'password',
        'role',
        'status',
        'phone_number',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
}