<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'raw_material_cost',
        'labour_cost',
        'power_consumption_cost',
        'packaging_cost',
        'transport_cost',
        'profit_margin',
        'final_cost_per_unit',
    ];

    protected $casts = [
        'raw_material_cost' => 'decimal:2',
        'labour_cost' => 'decimal:2',
        'power_consumption_cost' => 'decimal:2',
        'packaging_cost' => 'decimal:2',
        'transport_cost' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'final_cost_per_unit' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Calculate final cost per unit dynamically.
     * Formula: (Raw Material Cost + Labour + Power + Packaging + Transport + Profit Margin) / Output Quantity
     */
    public static function calculateUnitCost(
        float $rawMaterialCost,
        float $labourCost,
        float $powerCost,
        float $packagingCost,
        float $transportCost,
        float $profitMargin,
        float $outputQuantity
    ): float {
        if ($outputQuantity <= 0) {
            return 0.00;
        }

        $total = $rawMaterialCost + $labourCost + $powerCost + $packagingCost + $transportCost + $profitMargin;
        return round($total / $outputQuantity, 2);
    }
}
