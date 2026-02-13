<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->date('record_date');
            $table->integer('mortality_today')->default(0);
            $table->string('mortality_cause', 255)->nullable();
            $table->decimal('feed_consumed_kg', 10, 2)->default(0);
            $table->decimal('water_consumed_liters', 10, 2)->default(0);
            $table->integer('eggs_collected')->default(0);
            $table->integer('eggs_broken')->default(0);
            $table->decimal('average_weight_kg', 6, 3)->nullable();
            $table->enum('health_status', ['excellent', 'good', 'fair', 'poor', 'critical'])->default('good');
            $table->text('health_notes')->nullable();
            $table->decimal('temperature_celsius', 4, 1)->nullable();
            $table->decimal('humidity_percent', 4, 1)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['flock_id', 'record_date']);
            $table->unique(['flock_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_records');
    }
};
