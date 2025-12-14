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
        Schema::create('finance_intercompany_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number', 50)->unique();
            $table->foreignId('lender_company_id')->constrained('finance_companies')->onDelete('restrict');
            $table->foreignId('borrower_company_id')->constrained('finance_companies')->onDelete('restrict');
            $table->string('loan_date_bs', 10);
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('repaid_amount', 15, 2)->default(0);
            $table->decimal('outstanding_balance', 15, 2)->comment('loan_amount - repaid_amount');
            $table->decimal('interest_rate', 5, 2)->default(0)->comment('0.00 = interest free');
            $table->string('due_date_bs', 10)->nullable();
            $table->text('purpose')->nullable();
            $table->enum('status', ['active', 'partially_repaid', 'fully_repaid', 'written_off'])->default('active');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('fiscal_year_bs', 4);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('lender_company_id');
            $table->index('borrower_company_id');
            $table->index('status');
            $table->index('fiscal_year_bs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_intercompany_loans');
    }
};
