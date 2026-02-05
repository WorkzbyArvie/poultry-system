<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EggMonitoring extends Model
{
    use HasFactory;

    // Explicitly define the table with the schema prefix
    protected $table = 'laravel.egg_monitoring';

    // Your schema has a 'created_at' but likely not an 'updated_at'
    // If you get errors about 'updated_at', keep this as false
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'date_collected',
        'batch_source',
        'good_trays',
        'broken_eggs',
        'size_category',
        'recorded_by',
        'created_at',
    ];

    /**
     * Casting for specific data types
     */
    protected $casts = [
        'date_collected' => 'date',
        'good_trays' => 'integer',
        'broken_eggs' => 'integer',
        'created_at' => 'datetime',
    ];
}