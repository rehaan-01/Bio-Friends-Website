<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'output_quantity',
        'output_unit',
        'manufacturing_date',
    ];

    protected $casts = [
        'output_quantity' => 'decimal:3',
        'manufacturing_date' => 'date',
    ];

    /**
     * Get consumed raw material pivot entries.
     */
    public function materialConsumptions(): HasMany
    {
        return $this->hasMany(BatchMaterialConsumption::class);
    }

    /**
     * Get raw materials used in this batch.
     */
    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'batch_material_consumptions')
                    ->withPivot('quantity_consumed')
                    ->withTimestamps();
    }

    /**
     * Get the cost breakdown for this batch.
     */
    public function cost(): HasOne
    {
        return $this->hasOne(BatchCost::class);
    }

    /**
     * Get all sales for this batch.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
