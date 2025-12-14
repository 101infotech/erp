<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the enum to include 'period'
        DB::statement("ALTER TABLE hrm_leave_policies MODIFY COLUMN leave_type ENUM('annual', 'sick', 'casual', 'period', 'unpaid')");

        Schema::table('hrm_leave_policies', function (Blueprint $table) {
            $table->enum('gender_restriction', ['none', 'male', 'female'])->default('none')->after('leave_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_leave_policies', function (Blueprint $table) {
            $table->dropColumn('gender_restriction');
        });

        DB::statement("ALTER TABLE hrm_leave_policies MODIFY COLUMN leave_type ENUM('annual', 'sick', 'casual')");
    }
};
