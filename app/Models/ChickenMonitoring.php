<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChickenMonitoring extends Model
{
    use HasFactory;

    // Explicitly define the table with the schema prefix from your database
    protected $table = 'laravel.chicken_monitoring';

    // Disable timestamps if your table doesn't have 'updated_at' and 'created_at' columns
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * These match your table columns exactly.
     */
    protected $fillable = [
        'date_logged',
        'batch_name',
        'current_count',
        'mortality_count',
        'feed_consumed_bags',
        'health_status',
        'remarks',
        'recorded_by',
    ];

    /**
     * Casting for specific data types defined in your SQL
     */
    protected $casts = [
        'date_logged' => 'date',
        'feed_consumed_bags' => 'decimal:2',
        'current_count' => 'integer',
        'mortality_count' => 'integer',
    ];
}