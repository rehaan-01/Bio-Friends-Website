<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BatchCost;
use App\Models\BatchMaterialConsumption;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ManufacturingController extends Controller
{
    public function index()
    {
        return view('manufacturing.index');
    }

    public function create()
    {
        $rawMaterials = RawMaterial::all()->map(function ($rm) {
            return [
                'id' => $rm->id,
                'name' => $rm->name,
                'unit' => $rm->unit,
                'stock' => $rm->current_stock,
            ];
        });

        return view('manufacturing.create', compact('rawMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|unique:batches,batch_number',
            'output_quantity' => 'required|numeric|min:0.001',
            'output_unit' => 'required|in:Kg,Liter',
            'manufacturing_date' => 'required|date',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:raw_materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.001',
            'labour_cost' => 'nullable|numeric|min:0',
            'power_consumption_cost' => 'nullable|numeric|min:0',
            'packaging_cost' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Create Batch
            $batch = Batch::create([
                'batch_number' => $validated['batch_number'],
                'output_quantity' => $validated['output_quantity'],
                'output_unit' => $validated['output_unit'],
                'manufacturing_date' => $validated['manufacturing_date'],
            ]);

            // 2. Process Material Consumptions & Calculate Raw Material Cost
            $totalRawMaterialCost = 0.00;

            foreach ($validated['materials'] as $mat) {
                BatchMaterialConsumption::create([
                    'batch_id' => $batch->id,
                    'raw_material_id' => $mat['id'],
                    'quantity_consumed' => $mat['quantity'],
                ]);

                // Estimate raw material cost based on latest purchase price
                $rm = RawMaterial::with(['purchases' => function ($q) {
                    $q->latest('purchase_date');
                }])->find($mat['id']);

                $latestPrice = $rm->purchases->first() ? (float) $rm->purchases->first()->price : 0.00;
                $totalRawMaterialCost += ($mat['quantity'] * $latestPrice);
            }

            // 3. Extract Overheads
            $labourCost = (float) ($validated['labour_cost'] ?? 0);
            $powerCost = (float) ($validated['power_consumption_cost'] ?? 0);
            $packagingCost = (float) ($validated['packaging_cost'] ?? 0);
            $transportCost = (float) ($validated['transport_cost'] ?? 0);
            $profitMargin = (float) ($validated['profit_margin'] ?? 0);
            $outputQty = (float) $validated['output_quantity'];

            // 4. Calculate Final Cost Per Unit
            $finalCostPerUnit = BatchCost::calculateUnitCost(
                $totalRawMaterialCost,
                $labourCost,
                $powerCost,
                $packagingCost,
                $transportCost,
                $profitMargin,
                $outputQty
            );

            // 5. Store BatchCost
            BatchCost::create([
                'batch_id' => $batch->id,
                'raw_material_cost' => round($totalRawMaterialCost, 2),
                'labour_cost' => $labourCost,
                'power_consumption_cost' => $powerCost,
                'packaging_cost' => $packagingCost,
                'transport_cost' => $transportCost,
                'profit_margin' => $profitMargin,
                'final_cost_per_unit' => $finalCostPerUnit,
            ]);
        });

        return redirect()->route('manufacturing.index')->with('success', 'Production batch created and costed successfully.');
    }

    public function edit(Batch $batch)
    {
        $batch->load(['cost', 'materialConsumptions']);
        $rawMaterials = RawMaterial::all()->map(function ($rm) {
            return [
                'id' => $rm->id,
                'name' => $rm->name,
                'unit' => $rm->unit,
                'stock' => $rm->current_stock,
            ];
        });

        return view('manufacturing.edit', compact('batch', 'rawMaterials'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'batch_number' => 'required|string|unique:batches,batch_number,' . $batch->id,
            'output_quantity' => 'required|numeric|min:0.001',
            'output_unit' => 'required|in:Kg,Liter',
            'manufacturing_date' => 'required|date',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:raw_materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.001',
            'labour_cost' => 'nullable|numeric|min:0',
            'power_consumption_cost' => 'nullable|numeric|min:0',
            'packaging_cost' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($batch, $validated) {
            $batch->update([
                'batch_number' => $validated['batch_number'],
                'output_quantity' => $validated['output_quantity'],
                'output_unit' => $validated['output_unit'],
                'manufacturing_date' => $validated['manufacturing_date'],
            ]);

            $batch->materialConsumptions()->delete();
            $totalRawMaterialCost = 0.00;

            foreach ($validated['materials'] as $mat) {
                BatchMaterialConsumption::create([
                    'batch_id' => $batch->id,
                    'raw_material_id' => $mat['id'],
                    'quantity_consumed' => $mat['quantity'],
                ]);

                $rm = RawMaterial::with(['purchases' => function ($q) {
                    $q->latest('purchase_date');
                }])->find($mat['id']);

                $latestPrice = $rm->purchases->first() ? (float) $rm->purchases->first()->price : 0.00;
                $totalRawMaterialCost += ($mat['quantity'] * $latestPrice);
            }

            $labourCost = (float) ($validated['labour_cost'] ?? 0);
            $powerCost = (float) ($validated['power_consumption_cost'] ?? 0);
            $packagingCost = (float) ($validated['packaging_cost'] ?? 0);
            $transportCost = (float) ($validated['transport_cost'] ?? 0);
            $profitMargin = (float) ($validated['profit_margin'] ?? 0);
            $outputQty = (float) $validated['output_quantity'];

            $finalCostPerUnit = BatchCost::calculateUnitCost(
                $totalRawMaterialCost,
                $labourCost,
                $powerCost,
                $packagingCost,
                $transportCost,
                $profitMargin,
                $outputQty
            );

            $batch->cost()->updateOrCreate(
                ['batch_id' => $batch->id],
                [
                    'raw_material_cost' => round($totalRawMaterialCost, 2),
                    'labour_cost' => $labourCost,
                    'power_consumption_cost' => $powerCost,
                    'packaging_cost' => $packagingCost,
                    'transport_cost' => $transportCost,
                    'profit_margin' => $profitMargin,
                    'final_cost_per_unit' => $finalCostPerUnit,
                ]
            );
        });

        return redirect()->route('manufacturing.index')->with('success', 'Batch updated successfully.');
    }

    public function destroyBatch(Batch $batch)
    {
        DB::transaction(function () use ($batch) {
            $batch->sales()->delete();
            $batch->materialConsumptions()->delete();
            if ($batch->cost) {
                $batch->cost()->delete();
            }
            $batch->delete();
        });

        return redirect()->route('manufacturing.index')->with('success', 'Batch record deleted successfully.');
    }

    public function apiBatchData(): JsonResponse
    {
        $batches = Batch::with(['cost', 'rawMaterials'])->latest('manufacturing_date')->get()->map(function ($b) {
            $cost = $b->cost;
            return [
                'id' => $b->id,
                'batch_number' => $b->batch_number,
                'output_quantity' => (float) $b->output_quantity,
                'output_unit' => $b->output_unit,
                'manufacturing_date' => $b->manufacturing_date->format('Y-m-d'),
                'raw_material_cost' => $cost ? (float) $cost->raw_material_cost : 0.00,
                'labour_cost' => $cost ? (float) $cost->labour_cost : 0.00,
                'power_cost' => $cost ? (float) $cost->power_consumption_cost : 0.00,
                'packaging_cost' => $cost ? (float) $cost->packaging_cost : 0.00,
                'transport_cost' => $cost ? (float) $cost->transport_cost : 0.00,
                'profit_margin' => $cost ? (float) $cost->profit_margin : 0.00,
                'final_cost_per_unit' => $cost ? (float) $cost->final_cost_per_unit : 0.00,
                'total_batch_cost' => $cost ? round(((float) $cost->final_cost_per_unit * (float) $b->output_quantity), 2) : 0.00,
                'materials_used' => $b->rawMaterials->map(fn($rm) => $rm->name . ' (' . $rm->pivot->quantity_consumed . ' ' . $rm->unit . ')')->join(', '),
            ];
        });

        return response()->json($batches);
    }
}
