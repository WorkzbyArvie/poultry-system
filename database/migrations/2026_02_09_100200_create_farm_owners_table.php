<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('farm_name');
            $table->text('farm_address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('business_registration_number')->unique()->nullable();
            $table->enum('permit_status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->date('permit_expiry_date')->nullable();
            $table->enum('subscription_status', ['active', 'inactive', 'suspended'])->default('inactive');
            $table->decimal('monthly_revenue', 15, 2)->default(0);
            $table->integer('total_products')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('user_id');
            $table->index('permit_status');
            $table->index('subscription_status');
            $table->index('city');
            $table->index('province');
            $table->index('average_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_owners');
    }
};
