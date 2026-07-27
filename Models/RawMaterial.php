<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
    ];

    /**
     * Get all purchases for this raw material.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get all material consumption records for this raw material.
     */
    public function consumptions(): HasMany
    {
        return $this->hasMany(BatchMaterialConsumption::class);
    }

    /**
     * Get batches where this raw material was consumed.
     */
    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_material_consumptions')
                    ->withPivot('quantity_consumed')
                    ->withTimestamps();
    }

    /**
     * Accessor for total purchased quantity.
     */
    public function getTotalPurchasedAttribute(): float
    {
        return (float) $this->purchases()->sum('quantity');
    }

    /**
     * Accessor for total consumed quantity.
     */
    public function getTotalConsumedAttribute(): float
    {
        return (float) $this->consumptions()->sum('quantity_consumed');
    }

    /**
     * Accessor for calculated current stock.
     */
    public function getCurrentStockAttribute(): float
    {
        return round($this->total_purchased - $this->total_consumed, 3);
    }
}
