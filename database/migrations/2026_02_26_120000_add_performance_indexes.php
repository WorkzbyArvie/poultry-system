<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->index(['farm_owner_id', 'work_date', 'status'], 'attendance_farm_workdate_status_idx');
        });

        Schema::table('payroll', function (Blueprint $table) {
            $table->index(['farm_owner_id', 'status', 'pay_date'], 'payroll_farm_status_paydate_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['farm_owner_id', 'expense_date', 'payment_status'], 'expenses_farm_expensedate_paystatus_idx');
        });

        Schema::table('income_records', function (Blueprint $table) {
            $table->index(['farm_owner_id', 'income_date', 'category'], 'income_farm_incomedate_category_idx');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->index(['farm_owner_id', 'status', 'delivered_at'], 'deliveries_farm_status_deliveredat_idx');
            $table->index(['farm_owner_id', 'scheduled_date', 'status'], 'deliveries_farm_schedule_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('deliveries_farm_status_deliveredat_idx');
            $table->dropIndex('deliveries_farm_schedule_status_idx');
        });

        Schema::table('income_records', function (Blueprint $table) {
            $table->dropIndex('income_farm_incomedate_category_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_farm_expensedate_paystatus_idx');
        });

        Schema::table('payroll', function (Blueprint $table) {
            $table->dropIndex('payroll_farm_status_paydate_idx');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('attendance_farm_workdate_status_idx');
        });
    }
};
