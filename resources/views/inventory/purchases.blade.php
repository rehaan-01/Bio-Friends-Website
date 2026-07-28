@extends('layouts.app')

@section('title', 'Purchase Audit History - BioFriends Synergy Solutions')

@section('content')
<div class="space-y-6">
    
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('inventory.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">Inventory</a>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-xs font-bold text-blue-600">Purchase Audit History</span>
            </div>
            <h1 class="text-2xl font-heading font-extrabold text-slate-900 tracking-tight mt-1">Stock In (Purchases) Audit History</h1>
            <p class="text-slate-500 text-sm mt-0.5">Detailed purchase audit trail for all raw materials.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openModal('addPurchaseModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Log Purchase Entry (Stock In)</span>
            </button>
        </div>
    </div>

    <!-- AG Grid Section: Purchase History Log -->
    <div class="kb-card rounded-2xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-heading font-bold text-slate-900">Purchases Audit</h2>
                <p class="text-slate-500 text-xs mt-0.5">Interactive table sorting, filtering, and cost audit</p>
            </div>
            <div class="relative w-full sm:w-64">
                <input type="text" id="purchasesFilter" oninput="onPurchasesFilterChanged()" placeholder="Filter purchases..."
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div id="purchasesGrid" class="ag-theme-alpine w-full h-[520px] rounded-xl border border-slate-200"></div>
    </div>
</div>

<!-- Modal: Log Purchase Entry -->
<div id="addPurchaseModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 text-slate-900">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-heading font-bold text-lg text-slate-900">+ Log Purchase Entry (Stock In)</h3>
            <button onclick="closeModal('addPurchaseModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="{{ route('inventory.purchases.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Material</label>
                <select name="raw_material_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:bg-white">
                    <option value="">-- Choose Material --</option>
                    @foreach($rawMaterials as $mat)
                        <option value="{{ $mat->id }}">{{ $mat->name }} ({{ $mat->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Purchase Date</label>
                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:bg-white">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Quantity Added</label>
                    <input type="number" step="0.001" name="quantity" required placeholder="0.000" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Unit Price (₹)</label>
                    <input type="number" step="0.01" name="price" required placeholder="0.00" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:bg-white">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Supplier Bill / Invoice Ref</label>
                <input type="text" name="bill_data_reference" placeholder="e.g. Bill #INV-8829" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:bg-white">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeModal('addPurchaseModal')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">Save Purchase Entry</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Purchase Modal -->
<div id="editPurchaseModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 text-slate-900">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-heading font-bold text-lg text-slate-900">Edit Purchase Entry</h3>
            <button onclick="closeModal('editPurchaseModal')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="editPurchaseForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Raw Material</label>
                <select name="raw_material_id" id="edit_purchase_material_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                    @foreach($rawMaterials as $mat)
                        <option value="{{ $mat->id }}">{{ $mat->name }} ({{ $mat->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Purchase Date</label>
                <input type="date" name="purchase_date" id="edit_purchase_date" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Quantity</label>
                    <input type="number" step="0.001" name="quantity" id="edit_purchase_quantity" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Unit Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="edit_purchase_price" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Invoice / Reference</label>
                <input type="text" name="bill_data_reference" id="edit_purchase_ref" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm text-slate-900 focus:border-blue-600 focus:outline-none focus:bg-white">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="closeModal('editPurchaseModal')" class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">Update Purchase</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let purchasesGridApi;

    const purchasesColumnDefs = [
        { headerName: 'ID', field: 'id', width: 70, sortable: true },
        { headerName: 'Date', field: 'purchase_date', width: 120, sortable: true, filter: true },
        { headerName: 'Material', field: 'material_name', flex: 1, sortable: true, filter: true, cellClass: 'font-bold text-slate-900' },
        { 
            headerName: 'Quantity Added', 
            field: 'quantity', 
            width: 140, 
            sortable: true,
            cellClass: 'text-emerald-600 font-bold',
            valueFormatter: params => `+${params.value.toLocaleString()} ${params.data.unit}`
        },
        { 
            headerName: 'Unit Price', 
            field: 'price', 
            width: 120, 
            sortable: true,
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Total Cost', 
            field: 'total_cost', 
            width: 140, 
            sortable: true,
            cellClass: 'text-emerald-700 font-extrabold',
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { headerName: 'Invoice Ref', field: 'bill_data_reference', flex: 1, filter: true },
        {
            headerName: 'Actions',
            field: 'id',
            width: 140,
            cellRenderer: params => `
                <div class="flex items-center space-x-2 pt-0.5">
                    <button onclick="openEditPurchaseModal(${params.data.id}, ${params.data.raw_material_id}, ${params.data.quantity}, ${params.data.price}, '${(params.data.bill_data_reference || '').replace(/'/g, "\\'")}', '${params.data.purchase_date}')" class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-lg border border-emerald-200 font-bold">Edit</button>
                    <form action="/inventory/purchases/${params.data.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this purchase record?')" class="inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 px-2 py-0.5 rounded-lg border border-rose-200 font-bold">Delete</button>
                    </form>
                </div>
            `
        }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        const purchasesGridDiv = document.querySelector('#purchasesGrid');
        purchasesGridApi = agGrid.createGrid(purchasesGridDiv, {
            columnDefs: purchasesColumnDefs,
            rowData: [],
            pagination: true,
            paginationPageSize: 15,
            domLayout: 'normal'
        });

        fetch("{{ route('inventory.api.purchases') }}")
            .then(res => res.json())
            .then(data => purchasesGridApi.setGridOption('rowData', data));
    });

    function onPurchasesFilterChanged() {
        purchasesGridApi.setGridOption('quickFilterText', document.getElementById('purchasesFilter').value);
    }

    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openEditPurchaseModal(id, materialId, quantity, price, ref, date) {
        document.getElementById('editPurchaseForm').action = '/inventory/purchases/' + id;
        document.getElementById('edit_purchase_material_id').value = materialId;
        document.getElementById('edit_purchase_date').value = date;
        document.getElementById('edit_purchase_quantity').value = quantity;
        document.getElementById('edit_purchase_price').value = price;
        document.getElementById('edit_purchase_ref').value = ref === 'N/A' ? '' : ref;
        document.getElementById('editPurchaseModal').classList.remove('hidden');
    }
</script>
@endsection
