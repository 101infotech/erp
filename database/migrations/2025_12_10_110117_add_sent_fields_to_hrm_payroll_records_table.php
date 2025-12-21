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
            if (!Schema::hasColumn('hrm_payroll_records', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'sent_by')) {
                $table->unsignedBigInteger('sent_by')->nullable()->after('sent_at');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'sent_by_name')) {
                $table->string('sent_by_name')->nullable()->after('sent_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $columns = collect(['sent_at', 'sent_by', 'sent_by_name'])
                ->filter(fn($c) => Schema::hasColumn('hrm_payroll_records', $c))
                ->all();
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
