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
        Schema::create('lead_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->decimal('payment_amount', 15, 2);
            $table->date('payment_date');
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'check', 'online', 'other']);
            $table->string('reference_number', 100)->nullable();
            $table->unsignedBigInteger('received_by_id');
            $table->enum('payment_type', ['advance', 'partial', 'full']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('service_leads')->onDelete('cascade');
            $table->foreign('received_by_id')->references('id')->on('users');

            $table->index(['lead_id', 'payment_date']);
            $table->index('received_by_id');
            $table->index('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_payments');
    }
};
