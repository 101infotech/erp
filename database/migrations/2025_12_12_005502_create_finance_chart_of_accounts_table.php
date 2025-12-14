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
        Schema::create('finance_chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->foreignId('parent_account_id')->nullable()->constrained('finance_chart_of_accounts')->onDelete('set null');

            // Account Identification
            $table->string('account_code', 50)->unique();
            $table->string('account_name');
            $table->text('description')->nullable();

            // Account Classification
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->index();
            $table->enum('account_subtype', [
                // Assets
                'current_asset',
                'fixed_asset',
                'other_asset',
                // Liabilities
                'current_liability',
                'long_term_liability',
                'other_liability',
                // Equity
                'capital',
                'retained_earnings',
                'drawings',
                // Revenue
                'operating_revenue',
                'other_revenue',
                // Expenses
                'operating_expense',
                'cost_of_goods_sold',
                'other_expense',
                'depreciation'
            ])->nullable();

            // Account Properties
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('is_control_account')->default(false);
            $table->boolean('is_contra_account')->default(false);
            $table->boolean('allow_manual_entry')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system_account')->default(false);

            // Balance Tracking
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->string('fiscal_year_bs', 20)->nullable();

            // Hierarchy & Display
            $table->integer('level')->default(1);
            $table->integer('display_order')->default(0);

            // Tax & Reporting
            $table->boolean('is_taxable')->default(false);
            $table->string('tax_category')->nullable();
            $table->boolean('show_in_bs')->default(true); // Balance Sheet
            $table->boolean('show_in_pl')->default(true); // P&L Statement

            // Integration
            $table->string('external_code')->nullable();
            $table->json('metadata')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'account_type', 'is_active'], 'coa_company_type_active_idx');
            $table->index(['company_id', 'fiscal_year_bs'], 'coa_company_fiscal_idx');
            $table->index('account_code', 'coa_code_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_chart_of_accounts');
    }
};
