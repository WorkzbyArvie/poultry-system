<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_number', 50);
            $table->string('recipient_name', 255);
            $table->string('recipient_phone', 20);
            $table->text('delivery_address');
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time_from')->nullable();
            $table->time('scheduled_time_to')->nullable();
            $table->datetime('dispatched_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            $table->enum('status', ['pending', 'assigned', 'dispatched', 'in_transit', 'delivered', 'failed', 'returned', 'cancelled'])->default('pending');
            $table->string('failure_reason', 255)->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('cod_amount', 12, 2)->default(0);
            $table->boolean('cod_collected')->default(false);
            $table->string('proof_of_delivery_url', 500)->nullable();
            $table->text('delivery_notes')->nullable();
            $table->text('special_instructions')->nullable();
            $table->integer('delivery_attempts')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index(['order_id']);
            $table->index(['scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
