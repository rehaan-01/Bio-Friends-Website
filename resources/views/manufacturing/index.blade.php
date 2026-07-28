@extends('layouts.app')

@section('title', 'Manufacturing Batches - Biofriends')

@section('content')
<div class="space-y-6">

    <!-- Manufacturing Summary Banner -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Batches Manufactured -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-blue-600 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Batches Produced</span>
                <span class="text-2xl font-heading font-extrabold text-blue-600 mt-1 block font-mono">
                    {{ \App\Models\Batch::count() }} Production Batches
                </span>
                <span class="text-xs text-slate-500 font-medium mt-1 block">Formulated & Costed</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                🏭
            </div>
        </div>

        <!-- Total Manufacturing Cost -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-amber-500 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Production Cost</span>
                <span class="text-2xl font-heading font-extrabold text-amber-600 mt-1 block font-mono">
                    ₹{{ number_format(\App\Models\BatchCost::sum(\Illuminate\Support\Facades\DB::raw('final_cost_per_unit * (SELECT output_quantity FROM batches WHERE batches.id = batch_costs.batch_id)')), 2) }}
                </span>
                <span class="text-xs text-slate-500 font-medium mt-1 block">Materials + Operational Overheads</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-xl">
                ⚙️
            </div>
        </div>

        <!-- Create Batch Quick Action -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block font-heading">Formulate New Batch</span>
                <a href="{{ route('manufacturing.create') }}" class="inline-flex items-center space-x-2 mt-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Start New Production Batch</span>
                </a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl">
                ✨
            </div>
        </div>
    </div>

    <!-- AG Grid Section: Batches List -->
    <div class="kb-card rounded-2xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-heading font-extrabold text-slate-900">Manufactured Batches & Costing</h2>
                <p class="text-slate-500 text-xs mt-0.5">Comprehensive audit trail of batch yields, material consumption, and unit costing</p>
            </div>
            <div class="relative w-full sm:w-64">
                <input type="text" id="batchesFilter" oninput="onBatchesFilterChanged()" placeholder="Search batches..."
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <div id="batchesGrid" class="ag-theme-alpine w-full h-[500px] rounded-xl border border-slate-200"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let batchesGridApi;

    const batchColumnDefs = [
        { headerName: 'Batch #', field: 'batch_number', width: 140, sortable: true, filter: true, cellClass: 'font-mono text-blue-600 font-extrabold' },
        { headerName: 'Mfg Date', field: 'manufacturing_date', width: 120, sortable: true, filter: true },
        { 
            headerName: 'Output Yield', 
            field: 'output_quantity', 
            width: 140, 
            sortable: true,
            cellClass: 'font-bold text-slate-900',
            valueFormatter: params => `${params.value.toLocaleString()} ${params.data.output_unit}`
        },
        { headerName: 'Materials Consumed', field: 'materials_used', flex: 1, filter: true, cellClass: 'text-xs text-slate-700 font-medium' },
        { 
            headerName: 'Material Cost', 
            field: 'raw_material_cost', 
            width: 130, 
            sortable: true,
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Labour Cost', 
            field: 'labour_cost', 
            width: 110, 
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Power Cost', 
            field: 'power_cost', 
            width: 110, 
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Packaging', 
            field: 'packaging_cost', 
            width: 110, 
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Transport', 
            field: 'transport_cost', 
            width: 110, 
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Margin (₹)', 
            field: 'profit_margin', 
            width: 110, 
            cellClass: 'text-emerald-600 font-bold',
            valueFormatter: params => `₹${params.value.toFixed(2)}`
        },
        { 
            headerName: 'Final Unit Cost', 
            field: 'final_cost_per_unit', 
            width: 160, 
            sortable: true,
            cellRenderer: params => `<span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">₹${params.value.toFixed(2)} / ${params.data.output_unit}</span>`
        },
        {
            headerName: 'Actions',
            field: 'id',
            width: 140,
            cellRenderer: params => `
                <div class="flex items-center space-x-2 pt-0.5">
                    <a href="/manufacturing/${params.data.id}/edit" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-lg border border-blue-200 font-bold">Edit</a>
                    <form action="/manufacturing/${params.data.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete batch ${params.data.batch_number}?')" class="inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 px-2 py-0.5 rounded-lg border border-rose-200 font-bold">Delete</button>
                    </form>
                </div>
            `
        }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        const batchesGridDiv = document.querySelector('#batchesGrid');
        batchesGridApi = agGrid.createGrid(batchesGridDiv, {
            columnDefs: batchColumnDefs,
            rowData: [],
            pagination: true,
            paginationPageSize: 15,
            domLayout: 'normal'
        });

        fetch("{{ route('manufacturing.api.batches') }}")
            .then(res => res.json())
            .then(data => batchesGridApi.setGridOption('rowData', data));
    });

    function onBatchesFilterChanged() {
        batchesGridApi.setGridOption('quickFilterText', document.getElementById('batchesFilter').value);
    }
</script>
@endsection
