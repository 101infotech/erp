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
        Schema::create('finance_founder_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('founder_id')->constrained('finance_founders')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->string('transaction_number', 50)->unique();
            $table->string('transaction_date_bs', 10);
            $table->enum('transaction_type', ['investment', 'withdrawal']);
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->string('payment_reference')->nullable();
            $table->text('description')->nullable();
            $table->decimal('running_balance', 15, 2)->comment('Balance after this transaction');
            $table->boolean('is_settled')->default(false);
            $table->string('settled_date_bs', 10)->nullable();
            $table->string('document_path')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->string('fiscal_year_bs', 4);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('founder_id');
            $table->index('company_id');
            $table->index('transaction_type');
            $table->index('status');
            $table->index('fiscal_year_bs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_founder_transactions');
    }
};
