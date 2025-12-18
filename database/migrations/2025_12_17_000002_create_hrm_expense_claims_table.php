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
        Schema::create('hrm_expense_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->string('claim_number')->unique(); // Auto-generated claim number
            $table->enum('expense_type', ['travel', 'accommodation', 'meals', 'transportation', 'supplies', 'communication', 'other'])->default('other');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('NPR');
            $table->date('expense_date');
            $table->string('receipt_path')->nullable(); // File path for uploaded receipt
            $table->json('attachments')->nullable(); // Multiple receipts/documents
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('approved_by_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Payroll integration fields
            $table->foreignId('payroll_record_id')->nullable()->constrained('hrm_payroll_records')->onDelete('set null');
            $table->boolean('included_in_payroll')->default(false);
            $table->date('payroll_period_start')->nullable();
            $table->date('payroll_period_end')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();

            // Additional fields
            $table->string('project_code')->nullable(); // If expense is for a specific project
            $table->string('cost_center')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'status']);
            $table->index('status');
            $table->index('expense_type');
            $table->index('expense_date');
            $table->index('claim_number');
            $table->index('included_in_payroll');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_expense_claims');
    }
};
