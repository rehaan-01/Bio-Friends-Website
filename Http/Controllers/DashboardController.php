<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Purchase;
use App\Models\Batch;
use App\Models\BatchCost;
use App\Models\Dealer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRawMaterials = RawMaterial::count();
        $totalPurchasesValue = (float) Purchase::sum(DB::raw('quantity * price'));
        $totalBatches = Batch::count();
        $totalSalesValue = (float) Sale::sum('total_amount');
        $prepaidSales = (float) Sale::where('sale_type', 'Prepaid Sale')->sum('total_amount');
        $creditSales = (float) Sale::where('sale_type', 'Credit Sale')->sum('total_amount');
        $totalDealers = Dealer::count();

        $recentSales = Sale::with(['dealer', 'batch'])->latest('sale_date')->take(5)->get();
        $recentBatches = Batch::latest('manufacturing_date')->take(5)->get();

        return view('dashboard', compact(
            'totalRawMaterials',
            'totalPurchasesValue',
            'totalBatches',
            'totalSalesValue',
            'prepaidSales',
            'creditSales',
            'totalDealers',
            'recentSales',
            'recentBatches'
        ));
    }
}
