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
        Schema::create('finance_intercompany_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('finance_intercompany_loans')->onDelete('cascade');
            $table->string('payment_number', 50)->unique();
            $table->string('payment_date_bs', 10);
            $table->decimal('payment_amount', 15, 2);
            $table->string('payment_method');
            $table->string('payment_reference')->nullable();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('loan_id');
            $table->index('payment_date_bs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_intercompany_loan_payments');
    }
};
