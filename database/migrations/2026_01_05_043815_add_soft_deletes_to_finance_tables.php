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
        // Add soft deletes to critical finance tables for audit trail

        Schema::table('finance_companies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('finance_sales', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('finance_purchases', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('finance_founder_transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('finance_accounts', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('finance_budgets', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_companies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('finance_sales', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('finance_purchases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('finance_founder_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('finance_accounts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('finance_budgets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
