@extends('layouts.app')

@section('title', 'Executive Dashboard - BioFriends Synergy Solutions')

@section('content')
<div class="space-y-8">
    
    <!-- Agri-Bio Modern Hero Banner (Inspired by Modern Farming Visuals) -->
    <div class="relative rounded-3xl overflow-hidden shadow-xl border border-slate-800 text-white bg-slate-900">
        <!-- Background Image with Gradient Overlay -->
        <img src="{{ asset('images/hero-agri-green.jpg') }}" alt="Modern Bio Farming Background" class="absolute inset-0 w-full h-full object-cover object-center opacity-30 mix-blend-luminosity pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/90 to-emerald-950/75 pointer-events-none"></div>

        <div class="relative z-10 p-6 sm:p-10 lg:p-12 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="space-y-4 max-w-2xl">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                        [ STATISTICAL HIGHLIGHT ]
                    </span>
                    <span class="text-xs text-slate-300 font-semibold hidden sm:inline">ISO 9001:2026 Bio-Agri Certified</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-heading font-extrabold text-white tracking-tight leading-tight">
                    Measuring Growth and Progress in Modern Farming
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-normal">
                    Redefine the future of agriculture by innovation and efficiency. Agriculture is more than just food production—it's the backbone of community, economies, and sustainable ecosystems.
                </p>

                <!-- Feature Pills -->
                <div class="pt-2 flex flex-wrap items-center gap-3 text-xs font-semibold text-emerald-200">
                    <span class="flex items-center space-x-1.5 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                        <span class="text-emerald-400">🌱</span>
                        <span>Bio-Enzymes & Solubilizers</span>
                    </span>
                    <span class="flex items-center space-x-1.5 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                        <span class="text-emerald-400">🏭</span>
                        <span>Precision Batch Costing</span>
                    </span>
                    <span class="flex items-center space-x-1.5 bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                        <span class="text-emerald-400">💳</span>
                        <span>Khatabook Ledger Billing</span>
                    </span>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0">
                <a href="{{ route('manufacturing.create') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs px-5 py-3.5 rounded-xl transition shadow-lg flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Formulate New Batch</span>
                </a>
                <a href="{{ route('billing.index') }}" class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-5 py-3.5 rounded-xl border border-slate-700 transition flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>+ Issue Dealer Invoice</span>
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Metric 1: Total Sales Revenue -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-blue-600 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Revenue Billed</span>
                <span class="text-2xl font-heading font-extrabold text-blue-600 mt-1 block font-mono">₹{{ number_format($totalSalesValue, 2) }}</span>
                <span class="text-xs text-slate-500 font-medium mt-1 block">Across All Dealer Invoices</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                💳
            </div>
        </div>

        <!-- Metric 2: Stock Purchases In -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Total Purchases Logged</span>
                <span class="text-2xl font-heading font-extrabold text-emerald-600 mt-1 block font-mono">₹{{ number_format($totalPurchasesValue, 2) }}</span>
                <span class="text-xs text-slate-500 font-medium mt-1 block">{{ $totalRawMaterials }} Active Raw Materials</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl">
                📦
            </div>
        </div>

        <!-- Metric 3: Active Production Batches -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-amber-500 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Batches Manufactured</span>
                <span class="text-2xl font-heading font-extrabold text-slate-900 mt-1 block font-mono">{{ $totalBatches }} Batches</span>
                <span class="text-xs text-amber-700 font-semibold mt-1 block">Manufacturing Master</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-xl">
                ⚙️
            </div>
        </div>

        <!-- Metric 4: Registered Dealers -->
        <div class="kb-card rounded-2xl p-5 border-l-4 border-l-purple-600 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Dealer Directory</span>
                <span class="text-2xl font-heading font-extrabold text-purple-700 mt-1 block font-mono">{{ $totalDealers }} Dealers</span>
                <span class="text-xs text-purple-600 font-semibold mt-1 block">Prepaid & Credit Accounts</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xl">
                🏢
            </div>
        </div>

    </div>

    <!-- Agriculture Feature Visual Cards Showcase -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Showcase Card 1: Bio-Enzyme Modern Farming -->
        <div class="kb-card rounded-2xl overflow-hidden flex flex-col sm:flex-row group">
            <div class="sm:w-2/5 h-48 sm:h-auto relative overflow-hidden">
                <img src="{{ asset('images/hero-agri-green.jpg') }}" alt="Modern Bio Farming" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <span class="absolute top-3 left-3 bg-emerald-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-md">Who We Are</span>
            </div>
            <div class="sm:w-3/5 p-5 space-y-3 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-heading font-extrabold text-slate-900">Modern Bio-Enzyme Solutions</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">Enhance agricultural productivity with bio-fertilizers, solubilizers, and precision batch formulation management.</p>
                </div>
                <a href="{{ route('inventory.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center space-x-1">
                    <span>Manage Raw Stock →</span>
                </a>
            </div>
        </div>

        <!-- Showcase Card 2: Golden Wheat Yield & Crop Nutrition -->
        <div class="kb-card rounded-2xl overflow-hidden flex flex-col sm:flex-row group">
            <div class="sm:w-2/5 h-48 sm:h-auto relative overflow-hidden">
                <img src="{{ asset('images/hero-agri-golden.jpg') }}" alt="Agricultural Crop Nutrition" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <span class="absolute top-3 left-3 bg-amber-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-md">Distribution</span>
            </div>
            <div class="sm:w-3/5 p-5 space-y-3 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-heading font-extrabold text-slate-900">Agricultural Yield & Sales</h3>
                    <p class="text-slate-500 text-xs mt-1 leading-relaxed">Empowering crop distributors and farm suppliers across India with automated invoicing and credit tracking.</p>
                </div>
                <a href="{{ route('billing.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center space-x-1">
                    <span>View Dealer Billing →</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Live Activity Feeds -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Feed 1: Recent Dealer Sales -->
        <div class="kb-card rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-heading font-extrabold text-slate-900 flex items-center space-x-2">
                    <span>💳 Recent Dealer Invoices</span>
                </h3>
                <a href="{{ route('billing.index') }}" class="text-xs font-bold text-blue-600 hover:underline">View All →</a>
            </div>
            
            <div class="space-y-3">
                @forelse($recentSales as $sale)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between hover:bg-white hover:border-slate-300 transition">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">{{ $sale->dealer->name ?? 'Unknown Dealer' }}</span>
                            <span class="text-[11px] text-slate-500 block">Batch: {{ $sale->batch->batch_number ?? 'N/A' }} | {{ $sale->quantity_sold }} {{ $sale->batch->output_unit ?? '' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-extrabold text-emerald-600 block">₹{{ number_format($sale->total_amount, 2) }}</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $sale->sale_type === 'Prepaid Sale' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} inline-block">
                                {{ $sale->sale_type }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-slate-400">No sales recorded yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Feed 2: Recent Production Batches -->
        <div class="kb-card rounded-2xl p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-heading font-extrabold text-slate-900 flex items-center space-x-2">
                    <span>🏭 Recent Production Batches</span>
                </h3>
                <a href="{{ route('manufacturing.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">View All →</a>
            </div>

            <div class="space-y-3">
                @forelse($recentBatches as $batch)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between hover:bg-white hover:border-slate-300 transition">
                        <div>
                            <span class="text-xs font-mono font-bold text-slate-900 block">{{ $batch->batch_number }}</span>
                            <span class="text-[11px] text-slate-500 block">Yield: {{ $batch->output_quantity }} {{ $batch->output_unit }} | {{ $batch->manufacturing_date }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-extrabold text-slate-800 block">
                                ₹{{ number_format($batch->cost->final_cost_per_unit ?? 0, 2) }} / {{ $batch->output_unit }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Unit Cost</span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-slate-400">No manufacturing batches recorded yet.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
