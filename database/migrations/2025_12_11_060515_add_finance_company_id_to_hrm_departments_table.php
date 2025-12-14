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
        Schema::table('hrm_departments', function (Blueprint $table) {
            $table->foreignId('finance_company_id')->nullable()->after('company_id')->constrained('finance_companies')->onDelete('set null')->comment('Links department to finance company for salary allocation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_departments', function (Blueprint $table) {
            //
        });
    }
};
