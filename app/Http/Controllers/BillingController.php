<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Dealer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $dealers = Dealer::orderBy('name')->get();
        $batches = Batch::with('cost')->latest('manufacturing_date')->get();

        return view('billing.index', compact('dealers', 'batches'));
    }

    public function dealersIndex()
    {
        $dealers = Dealer::withCount('sales')->orderBy('name')->get();
        return view('billing.dealers', compact('dealers'));
    }

    public function createDealer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Dealer::create($validated);

        return redirect()->route('billing.index')->with('success', 'Dealer registered successfully.');
    }

    public function storeSale(Request $request)
    {
        $validated = $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'batch_id' => 'required|exists:batches,id',
            'quantity_sold' => 'required|numeric|min:0.001',
            'sale_type' => 'required|in:Credit Sale,Prepaid Sale',
            'sale_date' => 'required|date',
        ]);

        $batch = Batch::with('cost')->findOrFail($validated['batch_id']);
        $unitPrice = $batch->cost ? (float) $batch->cost->final_cost_per_unit : 0.00;
        $totalAmount = round($validated['quantity_sold'] * $unitPrice, 2);

        $sale = Sale::create([
            'dealer_id' => $validated['dealer_id'],
            'batch_id' => $validated['batch_id'],
            'quantity_sold' => $validated['quantity_sold'],
            'sale_type' => $validated['sale_type'],
            'total_amount' => $totalAmount,
            'sale_date' => $validated['sale_date'],
        ]);

        return redirect()->route('billing.invoice', $sale->id)->with('success', 'Sale recorded and invoice generated!');
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['dealer', 'batch.cost']);
        return view('billing.invoice', compact('sale'));
    }

    public function updateDealer(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $dealer->update($validated);

        return redirect()->route('billing.index')->with('success', 'Dealer updated successfully.');
    }

    public function destroyDealer(Dealer $dealer)
    {
        DB::transaction(function () use ($dealer) {
            $dealer->sales()->delete();
            $dealer->delete();
        });

        return redirect()->route('billing.index')->with('success', 'Dealer and related sales records deleted successfully.');
    }

    public function updateSale(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'batch_id' => 'required|exists:batches,id',
            'quantity_sold' => 'required|numeric|min:0.001',
            'sale_type' => 'required|in:Credit Sale,Prepaid Sale',
            'sale_date' => 'required|date',
        ]);

        $batch = Batch::with('cost')->findOrFail($validated['batch_id']);
        $unitPrice = $batch->cost ? (float) $batch->cost->final_cost_per_unit : 0.00;
        $totalAmount = round($validated['quantity_sold'] * $unitPrice, 2);

        $sale->update([
            'dealer_id' => $validated['dealer_id'],
            'batch_id' => $validated['batch_id'],
            'quantity_sold' => $validated['quantity_sold'],
            'sale_type' => $validated['sale_type'],
            'total_amount' => $totalAmount,
            'sale_date' => $validated['sale_date'],
        ]);

        return redirect()->route('billing.index')->with('success', 'Sale transaction updated successfully.');
    }

    public function destroySale(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('billing.index')->with('success', 'Sale record deleted successfully.');
    }

    public function apiSalesData(): JsonResponse
    {
        $sales = Sale::with(['dealer', 'batch.cost'])->latest('sale_date')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'dealer_id' => $s->dealer_id,
                'batch_id' => $s->batch_id,
                'invoice_no' => 'INV-' . str_pad($s->id, 5, '0', STR_PAD_LEFT),
                'dealer_name' => $s->dealer ? $s->dealer->name : 'N/A',
                'batch_number' => $s->batch ? $s->batch->batch_number : 'N/A',
                'quantity_sold' => (float) $s->quantity_sold,
                'unit' => $s->batch ? $s->batch->output_unit : '',
                'unit_price' => $s->batch && $s->batch->cost ? (float) $s->batch->cost->final_cost_per_unit : 0.00,
                'sale_type' => $s->sale_type,
                'total_amount' => (float) $s->total_amount,
                'sale_date' => $s->sale_date->format('Y-m-d'),
            ];
        });

        return response()->json($sales);
    }
}
