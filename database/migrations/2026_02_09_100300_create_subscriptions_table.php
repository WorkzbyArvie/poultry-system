<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained()->onDelete('cascade');
            $table->enum('plan_type', ['starter', 'professional', 'enterprise'])->default('starter');
            $table->decimal('monthly_cost', 10, 2);
            $table->integer('product_limit');
            $table->integer('order_limit')->nullable();
            $table->decimal('commission_rate', 5, 2); // Percentage
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('renewal_at')->nullable();
            $table->string('paymongo_subscription_id')->unique()->nullable();
            $table->string('paymongo_payment_method_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('farm_owner_id');
            $table->index('status');
            $table->index('plan_type');
            $table->index('ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
