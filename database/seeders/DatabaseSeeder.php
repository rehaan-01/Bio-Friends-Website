<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RawMaterial;
use App\Models\Purchase;
use App\Models\Batch;
use App\Models\BatchCost;
use App\Models\BatchMaterialConsumption;
use App\Models\Dealer;
use App\Models\Sale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Default Admin Account
        User::firstOrCreate(
            ['email' => 'admin@biofriends.com'],
            [
                'name' => 'BioFriends Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        // 1. Seed Raw Materials
        $mat1 = RawMaterial::create(['name' => 'Bio-Enzyme Extract Alpha', 'unit' => 'Liter']);
        $mat2 = RawMaterial::create(['name' => 'Organic Nitrogen Solubilizer', 'unit' => 'Kg']);
        $mat3 = RawMaterial::create(['name' => 'Emulsifying Agent E-40', 'unit' => 'Liter']);
        $mat4 = RawMaterial::create(['name' => 'Potassium Humate Granules', 'unit' => 'Kg']);

        // 2. Seed Purchases
        Purchase::create([
            'raw_material_id' => $mat1->id,
            'quantity' => 1500.000,
            'price' => 12.50,
            'bill_data_reference' => 'INV-SUP-2026-001',
            'purchase_date' => '2026-07-10',
        ]);
        Purchase::create([
            'raw_material_id' => $mat2->id,
            'quantity' => 2000.000,
            'price' => 8.20,
            'bill_data_reference' => 'INV-SUP-2026-002',
            'purchase_date' => '2026-07-12',
        ]);
        Purchase::create([
            'raw_material_id' => $mat3->id,
            'quantity' => 800.000,
            'price' => 15.00,
            'bill_data_reference' => 'INV-SUP-2026-003',
            'purchase_date' => '2026-07-15',
        ]);
        Purchase::create([
            'raw_material_id' => $mat4->id,
            'quantity' => 1200.000,
            'price' => 6.50,
            'bill_data_reference' => 'INV-SUP-2026-004',
            'purchase_date' => '2026-07-18',
        ]);

        // 3. Seed Production Batches
        $batch1 = Batch::create([
            'batch_number' => 'BATCH-20260720-001',
            'output_quantity' => 500.000,
            'output_unit' => 'Liter',
            'manufacturing_date' => '2026-07-20',
        ]);
        BatchMaterialConsumption::create([
            'batch_id' => $batch1->id,
            'raw_material_id' => $mat1->id,
            'quantity_consumed' => 300.000,
        ]);
        BatchMaterialConsumption::create([
            'batch_id' => $batch1->id,
            'raw_material_id' => $mat3->id,
            'quantity_consumed' => 100.000,
        ]);
        $rawCost1 = (300 * 12.50) + (100 * 15.00); // 3750 + 1500 = 5250
        $unitCost1 = BatchCost::calculateUnitCost($rawCost1, 250.00, 120.00, 180.00, 100.00, 500.00, 500.00);
        BatchCost::create([
            'batch_id' => $batch1->id,
            'raw_material_cost' => $rawCost1,
            'labour_cost' => 250.00,
            'power_consumption_cost' => 120.00,
            'packaging_cost' => 180.00,
            'transport_cost' => 100.00,
            'profit_margin' => 500.00,
            'final_cost_per_unit' => $unitCost1,
        ]);

        $batch2 = Batch::create([
            'batch_number' => 'BATCH-20260722-002',
            'output_quantity' => 750.000,
            'output_unit' => 'Kg',
            'manufacturing_date' => '2026-07-22',
        ]);
        BatchMaterialConsumption::create([
            'batch_id' => $batch2->id,
            'raw_material_id' => $mat2->id,
            'quantity_consumed' => 450.000,
        ]);
        BatchMaterialConsumption::create([
            'batch_id' => $batch2->id,
            'raw_material_id' => $mat4->id,
            'quantity_consumed' => 250.000,
        ]);
        $rawCost2 = (450 * 8.20) + (250 * 6.50); // 3690 + 1625 = 5315
        $unitCost2 = BatchCost::calculateUnitCost($rawCost2, 300.00, 150.00, 200.00, 135.00, 600.00, 750.00);
        BatchCost::create([
            'batch_id' => $batch2->id,
            'raw_material_cost' => $rawCost2,
            'labour_cost' => 300.00,
            'power_consumption_cost' => 150.00,
            'packaging_cost' => 200.00,
            'transport_cost' => 135.00,
            'profit_margin' => 600.00,
            'final_cost_per_unit' => $unitCost2,
        ]);

        // 4. Seed Dealers
        $d1 = Dealer::create([
            'name' => 'AgriBio Solutions India Pvt Ltd',
            'contact_info' => '+91 98765 43210 | info@agribio.in',
            'address' => 'Plot 45, Industrial Zone Sector 3, Hyderabad, TS',
        ]);
        $d2 = Dealer::create([
            'name' => 'Synergy Farm Supplies & Co',
            'contact_info' => '+91 91234 56789 | orders@synergyfarm.com',
            'address' => '88 Market Road, Pune, Maharashtra',
        ]);

        // 5. Seed Sales
        Sale::create([
            'dealer_id' => $d1->id,
            'batch_id' => $batch1->id,
            'quantity_sold' => 150.000,
            'sale_type' => 'Prepaid Sale',
            'total_amount' => round(150.000 * $unitCost1, 2),
            'sale_date' => '2026-07-24',
        ]);
        Sale::create([
            'dealer_id' => $d2->id,
            'batch_id' => $batch2->id,
            'quantity_sold' => 200.000,
            'sale_type' => 'Credit Sale',
            'total_amount' => round(200.000 * $unitCost2, 2),
            'sale_date' => '2026-07-25',
        ]);
    }
}
