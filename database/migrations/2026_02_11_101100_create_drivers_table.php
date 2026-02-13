<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('driver_code', 50);
            $table->string('name', 255);
            $table->string('phone', 20);
            $table->string('license_number', 50)->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->string('vehicle_plate', 20)->nullable();
            $table->string('vehicle_model', 100)->nullable();
            $table->enum('status', ['available', 'on_delivery', 'off_duty', 'on_leave', 'inactive'])->default('available');
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->integer('completed_deliveries')->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'status']);
            $table->unique(['farm_owner_id', 'driver_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
