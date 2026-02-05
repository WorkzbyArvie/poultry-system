<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('client_requests', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('owner_name');
        $table->string('farm_name');
        $table->text('farm_location');
        $table->string('valid_id_path');
        $table->string('business_permit_path');
        $table->string('status')->default('pending'); // pending, accepted, rejected
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('client_requests');
    }
};
