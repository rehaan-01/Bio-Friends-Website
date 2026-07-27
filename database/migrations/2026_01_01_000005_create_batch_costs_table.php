<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batch_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->unique()->constrained('batches')->cascadeOnDelete();
            $table->decimal('raw_material_cost', 12, 2);
            $table->decimal('labour_cost', 12, 2)->default(0.00);
            $table->decimal('power_consumption_cost', 12, 2)->default(0.00);
            $table->decimal('packaging_cost', 12, 2)->default(0.00);
            $table->decimal('transport_cost', 12, 2)->default(0.00);
            $table->decimal('profit_margin', 12, 2)->default(0.00);
            $table->decimal('final_cost_per_unit', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_costs');
    }
};
