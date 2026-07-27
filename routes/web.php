<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\BillingController;

// Protected ERP Routes
Route::middleware('auth')->group(function () {
    
    // Executive Dashboard Home
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Module 1: Inventory & Stock Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/purchases', [InventoryController::class, 'purchasesIndex'])->name('purchases.index');
        
        Route::post('/raw-materials', [InventoryController::class, 'storeRawMaterial'])->name('raw-materials.store');
        Route::put('/raw-materials/{rawMaterial}', [InventoryController::class, 'updateRawMaterial'])->name('raw-materials.update');
        Route::delete('/raw-materials/{rawMaterial}', [InventoryController::class, 'destroyRawMaterial'])->name('raw-materials.destroy');

        Route::post('/purchases', [InventoryController::class, 'storePurchase'])->name('purchases.store');
        Route::put('/purchases/{purchase}', [InventoryController::class, 'updatePurchase'])->name('purchases.update');
        Route::delete('/purchases/{purchase}', [InventoryController::class, 'destroyPurchase'])->name('purchases.destroy');

        Route::get('/api/stock', [InventoryController::class, 'apiStockData'])->name('api.stock');
        Route::get('/api/purchases', [InventoryController::class, 'apiPurchasesData'])->name('api.purchases');
    });

    // Module 2: Manufacturing & Costing
    Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
        Route::get('/', [ManufacturingController::class, 'index'])->name('index');
        Route::get('/create', [ManufacturingController::class, 'create'])->name('create');
        Route::post('/', [ManufacturingController::class, 'store'])->name('store');
        Route::get('/{batch}/edit', [ManufacturingController::class, 'edit'])->name('edit');
        Route::put('/{batch}', [ManufacturingController::class, 'update'])->name('update');
        Route::delete('/{batch}', [ManufacturingController::class, 'destroyBatch'])->name('destroy');
        Route::get('/api/batches', [ManufacturingController::class, 'apiBatchData'])->name('api.batches');
    });

    // Module 3: Billing & Sales
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/dealers', [BillingController::class, 'dealersIndex'])->name('dealers.index');
        
        Route::post('/dealers', [BillingController::class, 'createDealer'])->name('dealers.store');
        Route::put('/dealers/{dealer}', [BillingController::class, 'updateDealer'])->name('dealers.update');
        Route::delete('/dealers/{dealer}', [BillingController::class, 'destroyDealer'])->name('dealers.destroy');

        Route::post('/sales', [BillingController::class, 'storeSale'])->name('sales.store');
        Route::put('/sales/{sale}', [BillingController::class, 'updateSale'])->name('sales.update');
        Route::delete('/sales/{sale}', [BillingController::class, 'destroySale'])->name('sales.destroy');

        Route::get('/invoice/{sale}', [BillingController::class, 'invoice'])->name('invoice');
        Route::get('/api/sales', [BillingController::class, 'apiSalesData'])->name('api.sales');
    });

});

require __DIR__.'/auth.php';
