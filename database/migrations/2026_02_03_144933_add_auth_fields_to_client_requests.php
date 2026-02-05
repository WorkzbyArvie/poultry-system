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
    Schema::table('client_requests', function (Blueprint $table) {
        // Adding ->nullable() allows the old records to stay empty
        $table->string('email')->nullable()->unique()->after('farm_name');
        $table->string('password')->nullable()->after('business_permit_path');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_requests', function (Blueprint $table) {
            // This allows you to remove the columns if you roll back
            $table->dropColumn(['email', 'password']);
        });
    }
};