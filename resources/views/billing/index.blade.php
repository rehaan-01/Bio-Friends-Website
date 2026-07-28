@extends('layouts.app')

@section('title', 'Dealer Billing - BioFriends Synergy Solutions')

@section('content')
<div class="space-y-6">
    
    <!-- Module Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-slate-900 tracking-tight">Module 3: Dealer Billing & Sales Output</h1>
            <p class="text-slate-500 text-sm mt-0.5">Manage dealer sales invoices, record batch output sales, and print billing receipts.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="openModal('addSaleModal')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl transition shadow-sm flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>+ Issue Sale Invoice</span>
            </button>
            <a href="{{ route('billing.dealers.index') }}" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl transition shadow-sm flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Dealer Directory →</span>
            </a>
        </div>
    </div>

    <!-- Sub-navigation Tabs -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('billing.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-sm">
            Sales Invoices
        </a>
        <a href="{{ route('billing.dealers.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50">
            Dealer Directory ({{ count($dealers) }}) →
        </a>
    </div>

    <!-- Financial Summary Banner -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Sales -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-blue-600 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Sales Billed</span>
                <span class="text-2xl font-heading font-extrabold text-blue-600 mt-1 block font-mono">
                    ₹{{ number_format(\App\Models\Sale::sum('total_amount'), 2) }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                💳
            </div>
        </div>

        <!-- You Got -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">You Received (Prepaid Sales)</span>
                <span class="text-2xl font-heading font-extrabold text-emerald-600 mt-1 block font-mono">
                    ₹{{ number_format(\App\Models\Sale::where('sale_type', 'Prepaid Sale')->sum('total_amount'), 2) }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl">
                ↓
            </div>
        </div>

        <!-- You Will Get -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-amber-500 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Dealer Credit (Receivable)</span>
                <span class="text-2xl font-heading font-extrabold text-amber-600 mt-1 block font-mono">
                    ₹{{ number_format(\App\Models\Sale::where('sale_type', 'Credit Sale')->sum('total_amount'), 2) }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-xl">
                ⏳
            </div>
        </div>
    </div>

    <!-- AG Grid Table: Dealer Sales -->
    <div class="kb-card rounded-2xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-heading font-extrabold text-slate-900">Dealer Sales & Invoicing</h2>
                <p class="text-slate-500 text-xs mt-0.5">Record of all sales transactions with invoice links</p>
            </div>
            <div class="relative w-full sm:w-64">
                <input type="text" id="salesFilter" oninput="onSalesFilterChanged()" placeholder="Search transactions..."
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div id="salesGrid" class="ag-theme-alpine w-full h-[520px] rounded-xl border border-slate-200"></div>
    </div>
</div>

<!-- Modal: Issue Sale Invoice -->
<div id="addSaleModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 text-slate-900">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-heading font-bold text-lg text-slate-900">+ Issue Sale Invoice</h3>
            <button onclick="closeModal('addSaleModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="{{ route('billing.sales.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Dealer</label>
                <select name="dealer_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    <option value="">-- Choose Dealer --</option>
                    @foreach($dealers as $dealer)
                        <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Sale Date</label>
                <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Product Batch</label>
                <select name="batch_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    <option value="">-- Choose Batch --</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">
                            {{ $batch->batch_number }} ({{ $batch->output_quantity }} {{ $batch->output_unit }} @ ₹{{ $batch->cost ? number_format($batch->cost->final_cost_per_unit, 2) : '0.00' }}/unit)
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Quantity Sold</label>
                <input type="number" step="0.001" name="quantity_sold" required placeholder="0.000" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Payment Mode / Type</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center space-x-2 bg-slate-50 border border-slate-300 rounded-xl p-2.5 cursor-pointer hover:border-emerald-600">
                        <input type="radio" name="sale_type" value="Prepaid Sale" checked class="text-emerald-600">
                        <span class="text-xs font-bold text-emerald-700">Prepaid Sale</span>
                    </label>
                    <label class="flex items-center space-x-2 bg-slate-50 border border-slate-300 rounded-xl p-2.5 cursor-pointer hover:border-amber-600">
                        <input type="radio" name="sale_type" value="Credit Sale" class="text-amber-600">
                        <span class="text-xs font-bold text-amber-700">Credit Sale</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeModal('addSaleModal')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-sm">Generate Bill & Record</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Sale -->
<div id="editSaleModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 text-slate-900">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-heading font-bold text-lg text-slate-900">Edit Sale Transaction</h3>
            <button onclick="closeModal('editSaleModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="editSaleForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Dealer</label>
                <select name="dealer_id" id="edit_sale_dealer_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    @foreach($dealers as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Product Batch</label>
                <select name="batch_id" id="edit_sale_batch_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}">{{ $b->batch_number }} ({{ $b->output_quantity }} {{ $b->output_unit }} @ ₹{{ $b->cost ? number_format($b->cost->final_cost_per_unit, 2) : '0.00' }}/unit)</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Quantity Sold</label>
                    <input type="number" step="0.001" name="quantity_sold" id="edit_sale_quantity" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Sale Date</label>
                    <input type="date" name="sale_date" id="edit_sale_date" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Payment Type</label>
                <select name="sale_type" id="edit_sale_type" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    <option value="Prepaid Sale">Prepaid Sale (You Received)</option>
                    <option value="Credit Sale">Credit Sale (Receivable)</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeModal('editSaleModal')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-sm">Update Sale</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let salesGridApi;

    const salesColumnDefs = [
        { headerName: 'Invoice #', field: 'invoice_no', width: 120, sortable: true, filter: true, cellClass: 'font-mono text-blue-600 font-extrabold' },
        { headerName: 'Sale Date', field: 'sale_date', width: 120, sortable: true, filter: true },
        { headerName: 'Dealer Name', field: 'dealer_name', flex: 1, sortable: true, filter: true, cellClass: 'font-bold text-slate-900' },
        { headerName: 'Batch #', field: 'batch_number', width: 140, sortable: true, filter: true },
        { 
            headerName: 'Quantity Sold', 
            field: 'quantity_sold', 
            width: 140, 
            sortable: true,
            valueFormatter: params => `${params.value.toLocaleString()} ${params.data.unit}`
        },
        { 
            headerName: 'Unit Price', 
            field: 'unit_price', 
            width: 110, 
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Payment Status', 
            field: 'sale_type', 
            width: 140, 
            sortable: true,
            filter: true,
            cellRenderer: params => {
                const isPrepaid = params.value === 'Prepaid Sale';
                const badgeClass = isPrepaid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200';
                return `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold border ${badgeClass}">${params.value}</span>`;
            }
        },
        { 
            headerName: 'Total Bill Amount', 
            field: 'total_amount', 
            width: 150, 
            sortable: true,
            cellClass: 'text-blue-600 font-extrabold',
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Actions', 
            field: 'id', 
            width: 210,
            cellRenderer: params => `
                <div class="flex items-center space-x-1.5 pt-0.5">
                    <a href="/billing/invoice/${params.value}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-0.5 rounded-lg border border-blue-200 font-bold transition">Invoice</a>
                    <button onclick="openEditSaleModal(${params.data.id}, ${params.data.dealer_id}, ${params.data.batch_id}, ${params.data.quantity_sold}, '${params.data.sale_type}', '${params.data.sale_date}')" class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-2 py-0.5 rounded-lg border border-amber-200 font-bold">Edit</button>
                    <form action="/billing/sales/${params.value}" method="POST" onsubmit="return confirm('Are you sure you want to delete invoice ${params.data.invoice_no}?')" class="inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 px-2 py-0.5 rounded-lg border border-rose-200 font-bold">Delete</button>
                    </form>
                </div>
            `
        }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        const salesGridDiv = document.querySelector('#salesGrid');
        salesGridApi = agGrid.createGrid(salesGridDiv, {
            columnDefs: salesColumnDefs,
            rowData: [],
            pagination: true,
            paginationPageSize: 15,
            domLayout: 'normal'
        });

        fetch("{{ route('billing.api.sales') }}")
            .then(res => res.json())
            .then(data => salesGridApi.setGridOption('rowData', data));
    });

    function onSalesFilterChanged() {
        salesGridApi.setGridOption('quickFilterText', document.getElementById('salesFilter').value);
    }

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditSaleModal(id, dealerId, batchId, quantity, type, date) {
        document.getElementById('editSaleForm').action = '/billing/sales/' + id;
        document.getElementById('edit_sale_dealer_id').value = dealerId;
        document.getElementById('edit_sale_batch_id').value = batchId;
        document.getElementById('edit_sale_quantity').value = quantity;
        document.getElementById('edit_sale_type').value = type;
        document.getElementById('edit_sale_date').value = date;
        document.getElementById('editSaleModal').classList.remove('hidden');
    }
</script>
@endsection
