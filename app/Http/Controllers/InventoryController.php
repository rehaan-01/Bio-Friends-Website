<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Purchase;
use App\Models\Batch;
use App\Models\BatchMaterialConsumption;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::orderBy('name')->get();
        return view('inventory.index', compact('rawMaterials'));
    }

    public function purchasesIndex()
    {
        $rawMaterials = RawMaterial::orderBy('name')->get();
        return view('inventory.purchases', compact('rawMaterials'));
    }

    public function storeRawMaterial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:Kg,Liter',
            'initial_quantity' => 'nullable|numeric|min:0',
            'initial_price' => 'nullable|numeric|min:0',
        ]);

        $rawMaterial = RawMaterial::create([
            'name' => $validated['name'],
            'unit' => $validated['unit'],
        ]);

        if (!empty($validated['initial_quantity']) && $validated['initial_quantity'] > 0) {
            Purchase::create([
                'raw_material_id' => $rawMaterial->id,
                'quantity' => $validated['initial_quantity'],
                'price' => $validated['initial_price'] ?? 0.00,
                'bill_data_reference' => 'Opening Stock / Initial Registration',
                'purchase_date' => now()->format('Y-m-d'),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Raw material registered successfully' . (!empty($validated['initial_quantity']) ? ' with initial stock.' : '.'));
    }

    public function storePurchase(Request $request)
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|numeric|min:0.001',
            'price' => 'required|numeric|min:0',
            'bill_data_reference' => 'nullable|string',
            'purchase_date' => 'required|date',
        ]);

        Purchase::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Purchase logged successfully.');
    }

    public function updateRawMaterial(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:Kg,Liter',
            'total_purchased' => 'nullable|numeric|min:0',
            'total_consumed' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($rawMaterial, $validated, $request) {
            $rawMaterial->update([
                'name' => $validated['name'],
                'unit' => $validated['unit'],
            ]);

            // Adjust Unit Price if provided
            if ($request->has('unit_price') && $request->unit_price !== null) {
                $newPrice = (float) $validated['unit_price'];
                $latestPurchase = $rawMaterial->purchases()->latest('purchase_date')->latest('id')->first();
                if ($latestPurchase) {
                    $latestPurchase->update(['price' => $newPrice]);
                } else {
                    Purchase::create([
                        'raw_material_id' => $rawMaterial->id,
                        'quantity' => 0,
                        'price' => $newPrice,
                        'bill_data_reference' => 'Price Initialization',
                        'purchase_date' => now()->format('Y-m-d'),
                    ]);
                }
            }

            // Adjust Total Purchased if edited
            if ($request->has('total_purchased') && $request->total_purchased !== null) {
                $currentPurchased = (float) $rawMaterial->purchases()->sum('quantity');
                $targetPurchased = (float) $validated['total_purchased'];
                $diffPurchased = round($targetPurchased - $currentPurchased, 3);

                if ($diffPurchased != 0) {
                    Purchase::create([
                        'raw_material_id' => $rawMaterial->id,
                        'quantity' => $diffPurchased,
                        'price' => isset($validated['unit_price']) ? (float) $validated['unit_price'] : 0.00,
                        'bill_data_reference' => 'Manual Stock Adjustment',
                        'purchase_date' => now()->format('Y-m-d'),
                    ]);
                }
            }

            // Adjust Total Consumed if edited
            if ($request->has('total_consumed') && $request->total_consumed !== null) {
                $currentConsumed = (float) $rawMaterial->consumptions()->sum('quantity_consumed');
                $targetConsumed = (float) $validated['total_consumed'];
                $diffConsumed = round($targetConsumed - $currentConsumed, 3);

                if ($diffConsumed != 0) {
                    $adjBatch = Batch::firstOrCreate(
                        ['batch_number' => 'ADJUSTMENT-STOCK'],
                        [
                            'output_quantity' => 0,
                            'output_unit' => $rawMaterial->unit,
                            'manufacturing_date' => now()->format('Y-m-d'),
                        ]
                    );

                    BatchMaterialConsumption::create([
                        'batch_id' => $adjBatch->id,
                        'raw_material_id' => $rawMaterial->id,
                        'quantity_consumed' => $diffConsumed,
                    ]);
                }
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Raw material details, price, and stock levels updated successfully.');
    }

    public function destroyRawMaterial(RawMaterial $rawMaterial)
    {
        DB::transaction(function () use ($rawMaterial) {
            $rawMaterial->purchases()->delete();
            $rawMaterial->consumptions()->delete();
            $rawMaterial->delete();
        });

        return redirect()->route('inventory.index')->with('success', 'Raw material and related history deleted successfully.');
    }

    public function updatePurchase(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|numeric|min:0.001',
            'price' => 'required|numeric|min:0',
            'bill_data_reference' => 'nullable|string',
            'purchase_date' => 'required|date',
        ]);

        $purchase->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Purchase record updated successfully.');
    }

    public function destroyPurchase(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('inventory.index')->with('success', 'Purchase record deleted successfully.');
    }

    public function apiStockData(): JsonResponse
    {
        $materials = RawMaterial::with(['purchases', 'consumptions'])->get()->map(function ($material) {
            $totalPurchased = (float) $material->purchases->sum('quantity');
            $totalConsumed = (float) $material->consumptions->sum('quantity_consumed');
            $currentStock = round($totalPurchased - $totalConsumed, 3);

            $latestPurchase = $material->purchases->sortByDesc('id')->first();
            $unitPrice = $latestPurchase ? (float) $latestPurchase->price : 0.00;
            $totalValue = round($currentStock * $unitPrice, 2);

            return [
                'id' => $material->id,
                'name' => $material->name,
                'unit' => $material->unit,
                'unit_price' => $unitPrice,
                'total_purchased' => $totalPurchased,
                'total_consumed' => $totalConsumed,
                'current_stock' => $currentStock,
                'total_value' => $totalValue,
                'status' => $currentStock <= 0 ? 'Out of Stock' : ($currentStock < 50 ? 'Low Stock' : 'Adequate'),
            ];
        });

        return response()->json($materials);
    }

    public function apiPurchasesData(): JsonResponse
    {
        $purchases = Purchase::with('rawMaterial')->latest('purchase_date')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'raw_material_id' => $p->raw_material_id,
                'material_name' => $p->rawMaterial ? $p->rawMaterial->name : 'N/A',
                'unit' => $p->rawMaterial ? $p->rawMaterial->unit : '',
                'quantity' => (float) $p->quantity,
                'price' => (float) $p->price,
                'total_cost' => round((float) $p->quantity * (float) $p->price, 2),
                'bill_data_reference' => $p->bill_data_reference ?? 'N/A',
                'purchase_date' => $p->purchase_date->format('Y-m-d'),
            ];
        });

        return response()->json($purchases);
    }
}
