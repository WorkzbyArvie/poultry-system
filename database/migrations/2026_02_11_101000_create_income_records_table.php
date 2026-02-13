<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('income_number', 50);
            $table->enum('category', ['product_sales', 'egg_sales', 'chicken_sales', 'chick_sales', 'feed_sales', 'service_income', 'other']);
            $table->string('description', 500);
            $table->string('customer_name', 255)->nullable();
            $table->string('customer_contact', 100)->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->date('income_date');
            $table->enum('payment_status', ['pending', 'partial', 'received', 'cancelled'])->default('received');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'gcash', 'maya', 'credit'])->default('cash');
            $table->string('reference_number', 100)->nullable();
            $table->string('receipt_url', 500)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'income_date']);
            $table->index(['farm_owner_id', 'category']);
            $table->index(['farm_owner_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_records');
    }
};
