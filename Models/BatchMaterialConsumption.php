<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchMaterialConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'raw_material_id',
        'quantity_consumed',
    ];

    protected $casts = [
        'quantity_consumed' => 'decimal:3',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
