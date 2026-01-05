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
        Schema::create('service_leads', function (Blueprint $table) {
            $table->id();
            $table->string('service_requested');
            $table->string('location');
            $table->string('client_name');
            $table->string('phone_number');
            $table->string('email');
            $table->date('inspection_date')->nullable();
            $table->time('inspection_time')->nullable();
            $table->decimal('inspection_charge', 10, 2)->nullable();
            $table->date('inspection_report_date')->nullable();
            $table->foreignId('inspection_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('Intake');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('status');
            $table->index('inspection_date');
            $table->index('inspection_assigned_to');
            $table->index('created_at');
            $table->index('created_by');
            $table->index(['status', 'inspection_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_leads');
    }
};
