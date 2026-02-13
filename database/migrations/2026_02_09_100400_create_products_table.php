<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_owner_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', ['live_stock', 'breeding', 'fighting_cock', 'eggs', 'feeds', 'equipment', 'other']);
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('inactive');
            $table->integer('quantity_available');
            $table->integer('quantity_sold')->default(0);
            $table->decimal('price', 12, 2);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->text('attributes')->nullable(); // JSON: {breed, age, weight, etc}
            $table->string('unit')->default('piece'); // piece, kg, liter, etc
            $table->integer('minimum_order')->default(1);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('image_url')->nullable();
            $table->text('image_urls')->nullable(); // JSON array for multiple images
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('review_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index('farm_owner_id');
            $table->index('category');
            $table->index('status');
            $table->index('sku');
            $table->index('quantity_available');
            $table->index('average_rating');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
