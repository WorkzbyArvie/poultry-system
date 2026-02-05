<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These must match the columns in your migration file.
     */
    protected $fillable = [
    'owner_name',
    'farm_name',
    'email',           // Add this
    'farm_location',
    'valid_id_path',
    'business_permit_path',
    'password',        // Add this
    'status',
];
}