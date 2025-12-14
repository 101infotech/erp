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
        Schema::create('finance_journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('finance_journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('finance_chart_of_accounts')->onDelete('restrict');

            // Line Details
            $table->integer('line_number')->default(1);
            $table->text('description')->nullable();

            // Amounts
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);

            // Dimensions (for analytics)
            $table->foreignId('category_id')->nullable()->constrained('finance_categories')->onDelete('set null');
            $table->string('department')->nullable();
            $table->string('project')->nullable();
            $table->string('cost_center')->nullable();

            // Reference
            $table->string('reference')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['journal_entry_id', 'line_number']);
            $table->index('account_id');
            $table->index(['account_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_journal_entry_lines');
    }
};
