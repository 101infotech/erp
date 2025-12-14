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
        Schema::create('hrm_leave_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('hrm_companies')->cascadeOnDelete();
            $table->string('policy_name');
            $table->enum('leave_type', ['annual', 'sick', 'casual'])->index();
            $table->integer('annual_quota')->comment('Days per year');
            $table->boolean('carry_forward_allowed')->default(false);
            $table->integer('max_carry_forward')->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'leave_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_leave_policies');
    }
};
