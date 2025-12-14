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
        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->string('fiscal_year_bs', 4);
            $table->foreignId('category_id')->nullable()->constrained('finance_categories')->onDelete('cascade');
            $table->enum('budget_type', ['monthly', 'quarterly', 'annual']);
            $table->tinyInteger('period')->comment('1-12 for month, 1-4 for quarter');
            $table->decimal('budgeted_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('variance', 15, 2)->default(0)->comment('actual - budgeted');
            $table->decimal('variance_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'approved', 'active'])->default('draft');
            $table->timestamps();

            $table->index('company_id');
            $table->index('fiscal_year_bs');
            $table->index('category_id');
            $table->index('budget_type');
            $table->unique(['company_id', 'fiscal_year_bs', 'category_id', 'budget_type', 'period'], 'finance_budgets_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_budgets');
    }
};
