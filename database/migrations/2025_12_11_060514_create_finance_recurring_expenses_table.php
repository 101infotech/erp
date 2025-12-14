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
        Schema::create('finance_recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->string('expense_name');
            $table->foreignId('category_id')->nullable()->constrained('finance_categories')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->enum('frequency', ['monthly', 'quarterly', 'annually']);
            $table->string('start_date_bs', 10);
            $table->string('end_date_bs', 10)->nullable();
            $table->string('payment_method');
            $table->foreignId('account_id')->nullable()->constrained('finance_accounts')->onDelete('set null');
            $table->boolean('auto_create_transaction')->default(false);
            $table->string('last_generated_date_bs', 10)->nullable();
            $table->string('next_due_date_bs', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('company_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('next_due_date_bs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_recurring_expenses');
    }
};
