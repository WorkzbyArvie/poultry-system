<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_name',
        'quantity',
        'unit',
        'last_restocked_at',
        'notes',
    ];

    protected $casts = [
        'quantity'         => 'integer',
        'last_restocked_at' => 'datetime',
    ];

    /**
     * Get the user (client) that owns this inventory.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
