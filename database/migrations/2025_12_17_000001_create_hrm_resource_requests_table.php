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
        Schema::create('hrm_resource_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('category', ['office_supplies', 'equipment', 'pantry', 'furniture', 'technology', 'other'])->default('other');
            $table->enum('status', ['pending', 'approved', 'rejected', 'fulfilled', 'cancelled'])->default('pending');
            $table->text('reason')->nullable(); // Why the item is needed
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('approved_by_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('fulfilled_by_name')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->text('fulfillment_notes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->string('vendor')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['employee_id', 'status']);
            $table->index('status');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_resource_requests');
    }
};
