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
        Schema::create('finance_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['holding', 'subsidiary', 'independent'])->default('independent');
            $table->foreignId('parent_company_id')->nullable()->constrained('finance_companies')->onDelete('set null');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('pan_number')->nullable();
            $table->text('address')->nullable();
            $table->string('established_date_bs', 10)->nullable()->comment('YYYY-MM-DD format');
            $table->tinyInteger('fiscal_year_start_month')->default(4)->comment('4 = Shrawan (Nepali FY start)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_companies');
    }
};
