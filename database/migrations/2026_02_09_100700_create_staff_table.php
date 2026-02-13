<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('farm_owner_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('set null');
            $table->enum('staff_role', ['super_admin', 'admin', 'manager', 'warehouse', 'delivery', 'support']);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('permissions')->nullable(); // JSON array
            $table->timestamp('assigned_at');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('user_id');
            $table->index('farm_owner_id');
            $table->index('staff_role');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
