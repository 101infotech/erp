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
        Schema::create('finance_asset_depreciation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_asset_id')->constrained('finance_assets')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');

            // Depreciation Period
            $table->string('fiscal_year_bs', 10);
            $table->string('fiscal_month_bs', 7); // Format: 2082-08
            $table->string('depreciation_date_bs'); // Date of calculation

            // Depreciation Amounts
            $table->decimal('opening_book_value', 15, 2); // Book value at start of period
            $table->decimal('depreciation_amount', 15, 2); // Depreciation for this period
            $table->decimal('accumulated_depreciation', 15, 2); // Total depreciation to date
            $table->decimal('closing_book_value', 15, 2); // Book value after depreciation

            // Calculation Details
            $table->string('calculation_method'); // Method used for this entry
            $table->integer('period_number')->nullable(); // Period number in asset life
            $table->text('calculation_notes')->nullable();

            // Adjustment tracking
            $table->boolean('is_adjustment')->default(false);
            $table->decimal('adjustment_amount', 15, 2)->nullable();
            $table->text('adjustment_reason')->nullable();

            // Status
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('posted');
            $table->string('posted_date_bs')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index(['finance_asset_id', 'fiscal_year_bs', 'fiscal_month_bs'], 'asset_depr_period_idx');
            $table->index('company_id');
            $table->unique(['finance_asset_id', 'fiscal_year_bs', 'fiscal_month_bs'], 'asset_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_asset_depreciation');
    }
};
