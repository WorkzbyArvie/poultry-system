<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained('farm_owners')->cascadeOnDelete();
            $table->string('batch_name', 100);
            $table->string('breed_type', 100);
            $table->enum('flock_type', ['broiler', 'layer', 'breeder', 'native', 'fighting_cock']);
            $table->integer('initial_count');
            $table->integer('current_count');
            $table->integer('mortality_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->date('date_acquired');
            $table->integer('age_weeks')->default(0);
            $table->string('source', 255)->nullable();
            $table->decimal('acquisition_cost', 12, 2)->default(0);
            $table->enum('status', ['active', 'sold', 'culled', 'transferred'])->default('active');
            $table->string('housing_location', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['farm_owner_id', 'status']);
            $table->index(['farm_owner_id', 'flock_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flocks');
    }
};
