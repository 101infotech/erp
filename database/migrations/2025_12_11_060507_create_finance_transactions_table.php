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
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->string('transaction_number', 50)->unique();
            $table->string('transaction_date_bs', 10)->comment('YYYY-MM-DD format');
            $table->enum('transaction_type', ['income', 'expense', 'transfer', 'investment', 'loan']);
            $table->foreignId('category_id')->nullable()->constrained('finance_categories')->onDelete('set null');
            $table->foreignId('subcategory_id')->nullable()->constrained('finance_categories')->onDelete('set null');
            $table->string('reference_type')->nullable()->comment('payroll, sale, purchase, etc.');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->foreignId('debit_account_id')->nullable()->constrained('finance_accounts')->onDelete('set null');
            $table->foreignId('credit_account_id')->nullable()->constrained('finance_accounts')->onDelete('set null');
            $table->string('payment_method');
            $table->string('payment_reference')->nullable()->comment('Cheque number, transaction ID');
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('received_paid_by')->nullable()->comment('Person name from company');
            $table->boolean('is_from_holding_company')->default(false);
            $table->foreignId('fund_source_company_id')->nullable()->constrained('finance_companies')->onDelete('set null');
            $table->string('bill_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('document_path')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('fiscal_year_bs', 4)->comment('e.g., 2081');
            $table->tinyInteger('fiscal_month_bs')->comment('1-12');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('transaction_date_bs');
            $table->index('transaction_type');
            $table->index('category_id');
            $table->index('status');
            $table->index('fiscal_year_bs');
            $table->index('fiscal_month_bs');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
