<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('expense_number', 50);
            $table->enum('category', ['feeds', 'vaccines', 'medications', 'utilities', 'labor', 'equipment', 'maintenance', 'transportation', 'marketing', 'taxes', 'insurance', 'miscellaneous']);
            $table->string('subcategory', 100)->nullable();
            $table->string('description', 500);
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->date('expense_date');
            $table->date('due_date')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit', 'gcash', 'maya'])->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('receipt_url', 500)->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'expense_date']);
            $table->index(['farm_owner_id', 'category']);
            $table->index(['farm_owner_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
