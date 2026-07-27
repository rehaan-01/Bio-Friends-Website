<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Material;
use App\Services\StockService;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'mfg_date' => 'required|date',
            'output_quantity' => 'required|numeric|min:0.01',
            'labour_cost' => 'nullable|numeric|min:0',
            'power_cost' => 'nullable|numeric|min:0',
            'packaging_cost' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'profit_margin_percent' => 'nullable|numeric|min:0',
            'consumptions' => 'required|array|min:1',
            'consumptions.*.material_id' => 'required|exists:materials,id',
            'consumptions.*.quantity_consumed' => 'required|numeric|min:0.01',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $consumptions = $this->input('consumptions', []);
            $stockService = app(StockService::class);

            foreach ($consumptions as $index => $cData) {
                if (!isset($cData['material_id']) || !isset($cData['quantity_consumed'])) continue;

                $material = Material::find($cData['material_id']);
                if ($material) {
                    $qty = floatval($cData['quantity_consumed']);
                    $current = $stockService->currentStock($material);
                    if ($current < $qty) {
                        $validator->errors()->add("consumptions.{$index}.quantity_consumed", "Insufficient stock. Available: {$current}");
                    }
                }
            }
        });
    }
}
