<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained('hrm_companies')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->nullOnDelete();
            $table->string('jibble_person_id')->nullable()->unique();
            $table->string('full_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('code')->nullable()->unique();
            $table->string('position')->nullable();
            $table->date('join_date')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('jibble_person_id');
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_employees');
    }
};
