<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'price_per_unit' => 'required|numeric|min:0.01',
            'bill_number' => 'required|string|unique:raw_material_purchases,bill_number',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'bill_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
