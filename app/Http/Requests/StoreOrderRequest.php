<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;
use App\Services\StockService;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dealer_id' => 'required|exists:dealers,id',
            'order_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'nullable|exists:batches,id',
            'quantity_sold' => 'required|numeric|min:0.01',
            'rate_per_unit' => 'required|numeric|min:0.01',
            'sale_type' => 'required|in:prepaid,credit',
            'amount_paid' => 'nullable|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('product_id') && $this->has('quantity_sold')) {
                $product = Product::find($this->product_id);
                if ($product) {
                    $stockService = app(StockService::class);
                    $currentStock = $stockService->currentStock($product);
                    $qty = floatval($this->quantity_sold);

                    if ($currentStock < $qty) {
                        $validator->errors()->add('quantity_sold', "Insufficient stock. Available: {$currentStock}");
                    }
                }
            }
        });
    }
}
