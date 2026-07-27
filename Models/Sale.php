<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_id',
        'batch_id',
        'quantity_sold',
        'sale_type',
        'total_amount',
        'sale_date',
    ];

    protected $casts = [
        'quantity_sold' => 'decimal:3',
        'total_amount' => 'decimal:2',
        'sale_date' => 'date',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}
