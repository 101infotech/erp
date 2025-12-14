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
        Schema::create('finance_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');

            // Entry Identification
            $table->string('entry_number', 50)->unique();
            $table->string('reference_number')->nullable();
            $table->date('entry_date_bs');
            $table->string('fiscal_year_bs', 20)->index();
            $table->string('fiscal_month_bs', 2)->nullable();

            // Entry Type
            $table->enum('entry_type', [
                'manual',           // Manual journal entry
                'asset_purchase',   // Auto: Asset purchase
                'depreciation',     // Auto: Monthly depreciation
                'asset_disposal',   // Auto: Asset disposal
                'sales',            // Auto: From sales
                'purchase',         // Auto: From purchases
                'payroll',          // Auto: From payroll
                'payment',          // Auto: Payment transactions
                'receipt',          // Auto: Receipt transactions
                'adjustment',       // Adjustment entry
                'closing',          // Year-end closing
                'opening',          // Year opening
                'reversal'          // Reversal entry
            ])->default('manual')->index();

            // Source Reference
            $table->string('source_type')->nullable(); // Model name (FinanceAsset, FinanceSale, etc.)
            $table->unsignedBigInteger('source_id')->nullable(); // Source record ID
            $table->index(['source_type', 'source_id']);

            // Entry Details
            $table->text('description');
            $table->text('notes')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);

            // Status
            $table->enum('status', ['draft', 'posted', 'reversed', 'void'])->default('draft')->index();
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('posted_at')->nullable();

            // Reversal Tracking
            $table->foreignId('reversed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->foreignId('reversal_entry_id')->nullable()->constrained('finance_journal_entries')->onDelete('set null');

            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // Attachments
            $table->json('attachments')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'fiscal_year_bs', 'status'], 'je_company_fiscal_status_idx');
            $table->index(['company_id', 'entry_date_bs'], 'je_company_date_idx');
            $table->index('entry_number', 'je_number_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_journal_entries');
    }
};
