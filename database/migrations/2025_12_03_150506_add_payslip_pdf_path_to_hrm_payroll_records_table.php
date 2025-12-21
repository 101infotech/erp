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
            if (!Schema::hasColumn('hrm_payroll_records', 'payslip_pdf_path')) {
                $table->string('payslip_pdf_path')->nullable()->after('transaction_reference');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'approved_by_name')) {
                $table->string('approved_by_name')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $columns = collect(['payslip_pdf_path', 'approved_by_name'])
                ->filter(fn($c) => Schema::hasColumn('hrm_payroll_records', $c))
                ->all();
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
