<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_time_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('attendance_day_id')->nullable()->constrained('hrm_attendance_days')->cascadeOnDelete();
            $table->string('jibble_entry_id')->unique();
            $table->enum('type', ['In', 'Out']); // Clock In or Clock Out
            $table->dateTime('time'); // UTC time
            $table->dateTime('local_time'); // Local time
            $table->date('belongs_to_date');
            $table->string('project_id')->nullable();
            $table->string('project_name')->nullable();
            $table->string('activity_id')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('location_id')->nullable();
            $table->text('note')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'belongs_to_date']);
            $table->index('belongs_to_date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_time_entries');
    }
};
