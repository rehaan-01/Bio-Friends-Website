@extends('layouts.app')

@section('title', 'Invoice #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT) . ' - Biofriends Khatabook')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('billing.index') }}" class="text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center space-x-1 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Billing Ledger</span>
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition shadow-md flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print / Save Khatabook Bill PDF</span>
        </button>
    </div>

    <!-- Printable Invoice Card -->
    <div class="kb-card rounded-2xl p-8 text-slate-900 printable-area space-y-8">
        
        <!-- Company Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-6 gap-4">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="BioFriends Synergy Solutions Logo" class="h-12 w-auto object-contain">
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $sale->sale_type === 'Prepaid Sale' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                    {{ $sale->sale_type }}
                </span>
                <h2 class="text-xl font-mono font-extrabold text-slate-900 mt-2">INV-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-xs text-slate-500 font-medium">Date: {{ $sale->sale_date->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Bill To & From Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
            <div class="space-y-1">
                <span class="text-xs uppercase font-bold text-blue-600 tracking-wider">Customer / Dealer Account</span>
                <h3 class="font-bold text-slate-900 text-base">{{ $sale->dealer->name }}</h3>
                <p class="text-slate-500 text-xs">{{ $sale->dealer->contact_info ?? 'No contact specified' }}</p>
                <p class="text-slate-500 text-xs whitespace-pre-line">{{ $sale->dealer->address ?? 'N/A' }}</p>
            </div>
            <div class="space-y-1 md:text-right">
                <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Dispatched Batch Ledger</span>
                <h3 class="font-mono font-bold text-slate-900 text-sm">{{ $sale->batch->batch_number }}</h3>
                <p class="text-slate-500 text-xs">Mfg Date: {{ $sale->batch->manufacturing_date->format('M d, Y') }}</p>
                <p class="text-slate-500 text-xs">Output Unit: {{ $sale->batch->output_unit }}</p>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-bold uppercase text-slate-500 bg-slate-50">
                        <th class="py-3 px-4">Item Description</th>
                        <th class="py-3 px-4 text-center">Batch #</th>
                        <th class="py-3 px-4 text-right">Quantity</th>
                        <th class="py-3 px-4 text-right">Unit Price</th>
                        <th class="py-3 px-4 text-right">Total Bill Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <tr>
                        <td class="py-4 px-4 font-bold text-slate-900">
                            Biofriends Manufactured Yield Output
                            <span class="block text-xs font-normal text-slate-500">Green Chemical Formulation</span>
                        </td>
                        <td class="py-4 px-4 text-center font-mono text-xs text-blue-600 font-bold">{{ $sale->batch->batch_number }}</td>
                        <td class="py-4 px-4 text-right font-bold text-slate-900">{{ number_format($sale->quantity_sold, 3) }} {{ $sale->batch->output_unit }}</td>
                        <td class="py-4 px-4 text-right font-medium text-slate-600">₹{{ number_format($sale->batch->cost ? $sale->batch->cost->final_cost_per_unit : 0, 2) }}</td>
                        <td class="py-4 px-4 text-right font-extrabold text-blue-600 font-mono text-base">₹{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Invoice Total Summary -->
        <div class="flex justify-end border-t border-slate-100 pt-6">
            <div class="w-full sm:w-72 space-y-2">
                <div class="flex justify-between text-sm text-slate-500">
                    <span>Subtotal:</span>
                    <span class="font-mono">₹{{ number_format($sale->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-slate-500">
                    <span>Tax (0% Standard):</span>
                    <span class="font-mono">₹0.00</span>
                </div>
                <div class="flex justify-between text-lg font-extrabold text-slate-900 border-t border-slate-200 pt-2">
                    <span>Grand Total:</span>
                    <span class="text-blue-600 font-mono">₹{{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Signoff -->
        <div class="border-t border-slate-100 pt-6 text-center text-xs text-slate-500 space-y-1">
            <p class="font-bold text-slate-700">Biofriends Synergy Solutions Khatabook Ledger</p>
            <p>For support inquiries, contact billing@biofriendssynergy.com</p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; color: black !important; }
    .printable-area { background: white !important; color: black !important; border: none !important; box-shadow: none !important; }
    .printable-area * { color: black !important; }
}
</style>
@endsection
