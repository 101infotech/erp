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
            if (!Schema::hasColumn('hrm_payroll_records', 'period_start_ad')) {
                $table->date('period_start_ad')->nullable()->after('period_end_bs');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'period_end_ad')) {
                $table->date('period_end_ad')->nullable()->after('period_start_ad');
            }
        });

        // Add index if missing (outside Schema::table to allow conditional creation)
        $dbName = DB::getDatabaseName();
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'hrm_payroll_records')
            ->where('index_name', 'idx_employee_period_ad')
            ->exists();
        if (!$exists) {
            DB::statement('ALTER TABLE hrm_payroll_records ADD INDEX idx_employee_period_ad (employee_id, period_start_ad, period_end_ad)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        $dbName = DB::getDatabaseName();
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'hrm_payroll_records')
            ->where('index_name', 'idx_employee_period_ad')
            ->exists();
        if ($exists) {
            DB::statement('ALTER TABLE hrm_payroll_records DROP INDEX idx_employee_period_ad');
        }

        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $columns = collect(['period_start_ad', 'period_end_ad'])
                ->filter(fn($c) => Schema::hasColumn('hrm_payroll_records', $c))
                ->all();
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
