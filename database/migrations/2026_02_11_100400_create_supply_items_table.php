<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('sku', 50)->nullable();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('category', ['feeds', 'vitamins', 'vaccines', 'medications', 'equipment', 'supplements', 'cleaning', 'packaging', 'other']);
            $table->string('brand', 100)->nullable();
            $table->string('unit', 50);
            $table->decimal('quantity_on_hand', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->decimal('reorder_point', 12, 2)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->date('expiration_date')->nullable();
            $table->string('batch_number', 100)->nullable();
            $table->string('storage_location', 255)->nullable();
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'expired', 'discontinued'])->default('in_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'category']);
            $table->index(['farm_owner_id', 'status']);
            $table->index(['farm_owner_id', 'expiration_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_items');
    }
};
