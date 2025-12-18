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
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $table->json('expense_claims')->nullable()->after('allowances_total');
            $table->decimal('expense_claims_total', 10, 2)->default(0)->after('expense_claims');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $table->dropColumn(['expense_claims', 'expense_claims_total']);
        });
    }
};
