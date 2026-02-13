<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->foreignId('flock_id')->nullable()->constrained('flocks')->nullOnDelete();
            $table->foreignId('administered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['vaccine', 'medication', 'supplement', 'dewormer']);
            $table->string('name', 255);
            $table->string('brand', 100)->nullable();
            $table->string('batch_number', 100)->nullable();
            $table->decimal('dosage', 10, 3);
            $table->string('dosage_unit', 20);
            $table->enum('administration_method', ['drinking_water', 'injection', 'spray', 'eye_drop', 'feed_mix']);
            $table->date('date_administered');
            $table->date('next_due_date')->nullable();
            $table->integer('birds_treated')->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->enum('status', ['scheduled', 'completed', 'missed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->text('side_effects')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'date_administered']);
            $table->index(['farm_owner_id', 'next_due_date']);
            $table->index(['flock_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
