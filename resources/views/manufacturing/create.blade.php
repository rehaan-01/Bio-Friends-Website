@extends('layouts.app')

@section('title', 'Formulate Production Batch - Biofriends')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-slate-900">Formulate Production Batch</h1>
            <p class="text-slate-500 text-sm mt-0.5">Specify raw material inputs, operational overheads, and compute final unit cost.</p>
        </div>
        <a href="{{ route('manufacturing.index') }}" class="text-slate-600 hover:text-slate-900 text-xs font-bold flex items-center space-x-1 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Batches</span>
        </a>
    </div>

    <form action="{{ route('manufacturing.store') }}" method="POST" class="space-y-6" id="batchForm">
        @csrf

        <!-- 1. Basic Batch Information -->
        <div class="kb-card rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-heading font-bold text-blue-600 uppercase tracking-wider">Step 1: Batch Identification & Yield</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Batch Number</label>
                    <input type="text" name="batch_number" value="BATCH-{{ date('Ymd') }}-{{ rand(10,99) }}" required
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 font-mono focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Output Quantity Yield</label>
                    <input type="number" step="0.001" name="output_quantity" id="output_quantity" required placeholder="e.g. 500" oninput="calculateUnitCostPreview()"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Output Unit</label>
                    <select name="output_unit" required id="output_unit" onchange="calculateUnitCostPreview()"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                        <option value="Liter">Liters (Liter)</option>
                        <option value="Kg">Kilograms (Kg)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Manufacturing Date</label>
                <input type="date" name="manufacturing_date" value="{{ date('Y-m-d') }}" required
                       class="w-full md:w-1/3 bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
        </div>

        <!-- 2. Raw Material Consumption Selection -->
        <div class="kb-card rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-heading font-bold text-blue-600 uppercase tracking-wider">Step 2: Consumed Raw Materials</h2>
                <button type="button" onclick="addMaterialRow()" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-3 py-1.5 rounded-lg border border-blue-200 flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Add Material</span>
                </button>
            </div>

            <div id="materialsContainer" class="space-y-3">
                <div class="grid grid-cols-12 gap-3 items-center material-row">
                    <div class="col-span-7">
                        <select name="materials[0][id]" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                            <option value="">-- Choose Raw Material --</option>
                            @foreach($rawMaterials as $mat)
                                <option value="{{ $mat['id'] }}">{{ $mat['name'] }} (Available Stock: {{ $mat['stock'] }} {{ $mat['unit'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-4">
                        <input type="number" step="0.001" name="materials[0][quantity]" required placeholder="Quantity Consumed"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeMaterialRow(this)" class="text-rose-600 hover:text-rose-800 p-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Overhead Costs & Profit Margin -->
        <div class="kb-card rounded-2xl p-6 space-y-4">
            <h2 class="text-sm font-heading font-bold text-blue-600 uppercase tracking-wider">Step 3: Operational Overheads & Profit Margin (₹)</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Labour Cost (₹)</label>
                    <input type="number" step="0.01" name="labour_cost" id="labour_cost" placeholder="0.00" oninput="calculateUnitCostPreview()"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Power Consumption (₹)</label>
                    <input type="number" step="0.01" name="power_consumption_cost" id="power_cost" placeholder="0.00" oninput="calculateUnitCostPreview()"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Packaging Cost (₹)</label>
                    <input type="number" step="0.01" name="packaging_cost" id="packaging_cost" placeholder="0.00" oninput="calculateUnitCostPreview()"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Transport Cost (₹)</label>
                    <input type="number" step="0.01" name="transport_cost" id="transport_cost" placeholder="0.00" oninput="calculateUnitCostPreview()"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Profit Margin (₹)</label>
                    <input type="number" step="0.01" name="profit_margin" id="profit_margin" placeholder="0.00" oninput="calculateUnitCostPreview()"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
            </div>

            <!-- Cost Live Preview Banner -->
            <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between">
                <div>
                    <span class="text-xs uppercase tracking-wider text-slate-600 font-bold block">Calculated Total Overheads</span>
                    <span id="previewOverheads" class="text-lg font-extrabold text-slate-900">₹0.00</span>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase tracking-wider text-blue-700 font-bold block">Estimated Unit Cost</span>
                    <span id="previewUnitCost" class="text-2xl font-heading font-extrabold text-blue-700">₹0.00 / Unit</span>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-6 rounded-xl text-base transition shadow-md flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Save Production Batch</span>
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let materialIndex = 1;
    const rawMaterialOptions = `@foreach($rawMaterials as $mat)<option value="{{ $mat['id'] }}">{{ $mat['name'] }} (Available Stock: {{ $mat['stock'] }} {{ $mat['unit'] }})</option>@endforeach`;

    function addMaterialRow() {
        const container = document.getElementById('materialsContainer');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-3 items-center material-row';
        row.innerHTML = `
            <div class="col-span-7">
                <select name="materials[${materialIndex}][id]" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    <option value="">-- Choose Raw Material --</option>
                    ${rawMaterialOptions}
                </select>
            </div>
            <div class="col-span-4">
                <input type="number" step="0.001" name="materials[${materialIndex}][quantity]" required placeholder="Quantity Consumed"
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div class="col-span-1 text-center">
                <button type="button" onclick="removeMaterialRow(this)" class="text-rose-600 hover:text-rose-800 p-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        materialIndex++;
    }

    function removeMaterialRow(btn) {
        const rows = document.querySelectorAll('.material-row');
        if (rows.length > 1) {
            btn.closest('.material-row').remove();
        } else {
            alert('At least one raw material must be specified.');
        }
    }

    function calculateUnitCostPreview() {
        const labour = parseFloat(document.getElementById('labour_cost').value) || 0;
        const power = parseFloat(document.getElementById('power_cost').value) || 0;
        const packaging = parseFloat(document.getElementById('packaging_cost').value) || 0;
        const transport = parseFloat(document.getElementById('transport_cost').value) || 0;
        const profit = parseFloat(document.getElementById('profit_margin').value) || 0;
        const outputQty = parseFloat(document.getElementById('output_quantity').value) || 0;
        const unit = document.getElementById('output_unit').value;

        const totalOverheads = labour + power + packaging + transport + profit;
        document.getElementById('previewOverheads').innerText = '₹' + totalOverheads.toFixed(2);

        if (outputQty > 0) {
            const unitCost = (totalOverheads / outputQty);
            document.getElementById('previewUnitCost').innerText = '₹' + unitCost.toFixed(2) + ' / ' + unit;
        } else {
            document.getElementById('previewUnitCost').innerText = '₹0.00 / ' + unit;
        }
    }
</script>
@endsection
