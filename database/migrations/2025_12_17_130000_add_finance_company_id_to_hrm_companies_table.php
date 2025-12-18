<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hrm_companies', function (Blueprint $table): void {
            $table->unsignedBigInteger('finance_company_id')->nullable()->unique()->after('address');
            $table->foreign('finance_company_id')
                ->references('id')
                ->on('finance_companies')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('hrm_companies', function (Blueprint $table): void {
            $table->dropForeign(['finance_company_id']);
            $table->dropUnique(['finance_company_id']);
            $table->dropColumn('finance_company_id');
        });
    }
};
